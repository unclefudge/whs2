<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use Validator;

use DB;
use PDF;
use Mail;
use Session;
use App\User;
use App\Models\Site\Planner\Task;
use App\Models\Site\Planner\Trade;
use App\Models\Site\Site;
use App\Models\Site\SiteQa;
use App\Models\Site\SiteQaItem;
use App\Models\Site\SiteQaAction;
use App\Models\Site\Planner\SitePlanner;
use App\Models\Company\Company;
use App\Models\Comms\Todo;
use App\Models\Comms\TodoUser;
use App\Http\Requests;
use App\Http\Requests\Site\SiteQaRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Yajra\Datatables\Datatables;
use nilsenj\Toastr\Facades\Toastr;
use Carbon\Carbon;

/**
 * Class SiteQaController
 * @package App\Http\Controllers\Site
 */
class SiteQaController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasAnyPermissionType('site.qa'))
            return view('errors/404');

        return view('site/qa/list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /// Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.site.qa'))
            return view('errors/404');

        return view('site/qa/create');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $qa = SiteQa::findOrFail($id);

        /// Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('view.site.qa', $qa))
            return view('errors/404');

        return view('site/qa/show', compact('qa'));
    }

    /**
     * Edit the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $qa = SiteQa::findOrFail($id);

        /// Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site.qa', $qa))
            return view('errors/404');

        return view('site/qa/edit', compact('qa'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(SiteQaRequest $request)
    {
        /// Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.site.qa'))
            return view('errors/404');

        $qa_request = $request->all();

        // Create Site QA
        $newQA = SiteQa::create($qa_request);

        $order = 1;
        for ($i = 1; $i <= 25; $i ++) {
            if ($request->get("item$i")) {
                $super = ($request->has("super$i")) ? '1' : '0';
                $newItem = SiteQaItem::create(
                    ['doc_id'  => $newQA->id,
                     'task_id' => $request->get("task$i"),
                     'name'    => $request->get("item$i"),
                     'super'   => $super,
                     'order'   => $order ++,
                     'master'  => '1',
                    ]);
            }
        }
        Toastr::success("Created new template");

        return redirect('/site/qa/');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SiteQaRequest $request, $id)
    {
        $qa = SiteQa::findOrFail($id);

        /// Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site.qa', $qa))
            return view('errors/404');

        $qa_request = $request->all();
        //dd($qa_request);

        // Increment minor version
        list($major, $minor) = explode('.', $qa->version);
        $minor ++;
        $qa_request['version'] = $major . '.' . $minor;

        $qa->update($qa_request);

        // Delete existing Items
        foreach ($qa->items as $item)
            SiteQaItem::where('doc_id', $qa->id)->delete();

        // Re-create new ones
        $order = 1;
        for ($i = 1; $i <= 25; $i ++) {
            if ($request->get("item$i")) {
                $super = ($request->has("super$i")) ? '1' : '0';
                $newItem = SiteQaItem::create(
                    ['doc_id'  => $qa->id,
                     'task_id' => $request->get("task$i"),
                     'name'    => $request->get("item$i"),
                     'super'   => $super,
                     'order'   => $order ++,
                     'master'  => '1',
                    ]);
            }
        }

        Toastr::success("Updated Report");

        return redirect('site/qa/' . $qa->id . '/edit');
    }


    /**
     * Update Status the specified resource in storage.
     */
    public function updateReport(Request $request, $id)
    {
        $qa = SiteQa::findOrFail($id);
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site.qa', $qa))
            return view('errors/404');

        // Only Allow Ajax requests
        if ($request->ajax()) {
            $qa_request = $request->all();

            // Determine if report being signed off
            $signoff = $request->get('signoff');
            if ($signoff == 'super') {
                $qa_request['supervisor_sign_by'] = Auth::user()->id;
                $qa_request['supervisor_sign_at'] = Carbon::now();

                // Close any outstanding ToDos for supervisors and Create one for Area Super / Con Mgr
                $qa->closeToDo(Auth::user());
                if (!$qa->manager_sign_by) {
                    $site = Site::findOrFail($qa->site_id);
                    $qa->createManagerSignOffToDo($site->areaSupervisors()->pluck('id')->toArray());
                }
            }
            if ($signoff == 'manager') {
                $qa_request['manager_sign_by'] = Auth::user()->id;
                $qa_request['manager_sign_at'] = Carbon::now();
                // Close any outstanding ToDos for Area Super / Con Mgr
                $qa->closeToDo(Auth::user());
            }

            // If report was placed On Hold then auto add an Action + close ToDoo
            if ($request->get('status') == 2 && $qa->status != 2)
                $qa->moveToHold(Auth::user());

            // If report was reactived then auto add an Action + create ToDoo
            if ($request->get('status') == 1 && $qa->status != 1)
                $qa->moveToActive(Auth::user());

            // If report was marked Not Required then close ToDoo
            if ($request->get('status') == - 1)
                $qa->closeToDo(Auth::user());

            $qa->update($qa_request);

            // Determine if Report Signed Off and if so mark completed
            if ($qa->supervisor_sign_by && $qa->manager_sign_by) {
                $qa->status = 0;
                $qa->save();
                if ($qa->master_id == '74')  // Only email if QA is Handover template  ie. final QA on site
                    $qa->emailCompleted();
            }
            Toastr::success("Updated Report");

            return $qa;
        }

        return view('errors/404');
    }

    /**
     * Update Item the specified resource in storage.
     *
     */
    public function updateItem(Request $request, $id)
    {
        $item = SiteQaItem::findOrFail($id);
        $qa = SiteQa::findOrFail($item->doc_id);
        // Check authorisation and throw 404 if not
        //if (!Auth::user()->allowed2('edit.site.qa', $qa))
        //    return view('errors/404');

        $item_request = $request->only(['status', 'done_by']);

        // Update resolve date if just modified
        if (!$request->has('status')) {
            $item->status = 0;
            $item->sign_by = null;
            $item->save();
        } else {
            if ($item_request['status'] == '1' && $item->status != 1) {
                $item_request['sign_by'] = Auth::user()->id;
                $item_request['sign_at'] = Carbon::now()->toDateTimeString();
            }
            $item->update($item_request);
        }


        // Update modified timestamp on QA Doc
        $qa = SiteQa::findOrFail($item->doc_id);
        $qa->touch();

        Toastr::success("Updated record");

        return $item;
    }


    /**
     * Get QA Reports current user is authorised to manage + Process datatables ajax request.
     */
    public function getQaReports(Request $request)
    {
        $site_list = Auth::user()->authSites('view.site.qa')->pluck('id')->toArray();
        $records = DB::table('site_qa AS q')
            ->select(['q.id', 'q.name', 'q.site_id', 'q.version', 'q.company_id', 'q.status', 'q.updated_at',
                's.name as sitename'])
            ->join('sites AS s', 'q.site_id', '=', 's.id')
            ->where('q.company_id', Auth::user()->company_id)
            ->where('q.master', '0')
            ->whereIn('q.site_id', $site_list)
            ->where('q.status', $request->get('status'));

        $dt = Datatables::of($records)
            ->editColumn('id', '<div class="text-center"><a href="/site/qa/{{$id}}"><i class="fa fa-search"></i></a></div>')
            ->editColumn('sitename', function ($doc) {
                /*if ($doc->status == 1) {
                    $site = Site::find($doc->site_id);
                    $now = Carbon::now();
                    $weekago = $now->subWeek()->toDateTimeString();
                    if ($doc->updated_at <= $weekago)
                        return "<span class='font-red'>$site->name</span>";
                }*/
                return $doc->sitename;
            })
            ->editColumn('name', function ($doc) {
                $name = $doc->name . ' &nbsp;<span class="font-grey-silver">v' . $doc->version . '</span>';
                /*if ($doc->status == 1) {
                    $now = Carbon::now();
                    $weekago = $now->subWeek()->toDateTimeString();
                    if ($doc->updated_at <= $weekago)
                        $name = '<span class="font-red">'.$name.'</span>';
                }*/

                return $name;
            })
            ->addColumn('supervisor', function ($doc) {
                $site = Site::find($doc->site_id);

                return $site->supervisorsSBC();
            })
            ->editColumn('updated_at', function ($doc) {
                if ($doc->status == 1) {
                    $now = Carbon::now();
                    $weekago = $now->subWeek()->toDateTimeString();
                    if ($doc->updated_at <= $weekago)
                        return '<span class="font-red">' . (new Carbon($doc->updated_at))->format('d/m/Y') . '</span>';
                }

                return (new Carbon($doc->updated_at))->format('d/m/Y');
            })
            ->addColumn('completed', function ($doc) {
                $qa = SiteQa::find($doc->id);
                $total = $qa->items()->count();
                $completed = $qa->itemsCompleted()->count();
                $pending = '';
                if ($qa->status != 0) {
                    if (Auth::user()->allowed2('edit.site.qa', $qa)) {
                        if ($total == $completed && $total != 0) {
                            $label_type = ($qa->supervisor_sign_by && $qa->manager_sign_by) ? 'label-success' : 'label-warning';
                            if (!$qa->supervisor_sign_by)
                                $pending = '<br><span class="badge badge-info badge-roundless pull-right">Pending Supervisor</span>';
                            elseif (!$qa->manager_sign_by)
                                $pending = '<br><span class="badge badge-primary badge-roundless pull-right">Pending Manager</span>';
                        } else
                            $label_type = 'label-danger';

                        return '<span class="label pull-right ' . $label_type . '">' . $completed . ' / ' . $total . '</span>' . $pending;
                    }
                }

                return '<span class="label pull-right label-success">' . $completed . ' / ' . $total . '</span>';
            })
            ->addColumn('action', function ($qa) {
                if (($qa->status && Auth::user()->allowed2('edit.site.qa', $qa)) || (!$qa->status && Auth::user()->allowed2('sig.site.qa', $qa)))
                    return '<a href="/site/qa/' . $qa->id . '" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom"><i class="fa fa-pencil"></i> Edit</a>';

                return '<a href="/site/qa/' . $qa->id . '" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom"><i class="fa fa-search"></i> View</a>';

            })
            ->rawColumns(['id', 'name', 'updated_at', 'completed', 'action'])
            ->make(true);

        return $dt;
    }

    /**
     * Get QA templates current user is authorised to manage + Process datatables ajax request.
     */
    public function getQaTemplates(Request $request)
    {
        $records = DB::table('site_qa')
            ->select(['id', 'name', 'version', 'company_id', 'status', 'updated_at'])
            ->where('company_id', Auth::user()->company_id)
            ->where('master', '1')
            ->where('status', $request->get('status'));

        $dt = Datatables::of($records)
            ->editColumn('id', '<div class="text-center"><a href="/site/qa/{{$id}}"><i class="fa fa-search"></i></a></div>')
            ->editColumn('name', function ($qa) {
                $name = $qa->name . ' &nbsp;<span class="font-grey-silver">v' . $qa->version . '</span>';
                if ($qa->status == 1) {
                    $now = Carbon::now();
                    $yearago = $now->subYear()->toDateTimeString();
                    if ($qa->updated_at < $yearago)
                        $name .= ' <span class="badge badge-danger badge-roundless">Out of Date</span>';
                }

                return $name;
            })
            ->editColumn('updated_at', function ($doc) {
                return (new Carbon($doc->updated_at))->format('d/m/Y');
            })
            ->addColumn('action', function ($doc) {
                $qa = SiteQa::find($doc->id);
                if (Auth::user()->allowed2('edit.site.qa', $qa))
                    return '<a href="/site/qa/' . $qa->id . '/edit" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom"><i class="fa fa-pencil"></i> Edit</a>';

                return '<a href="/site/qa/' . $qa->id . '" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom"><i class="fa fa-search"></i> View</a>';

            })
            ->rawColumns(['id', 'name', 'updated_at', 'completed', 'action'])
            ->make(true);

        return $dt;
    }


    /**
     * Display the specified resource.
     */
    public function exportQA()
    {
        return view('site/export/qa');
    }

    public function qaPDF(Request $request)
    {
        $site = Site::findOrFail($request->get('site_id'));

        $data = [];
        $site_qa = SiteQa::where('site_id', $site->id)->where('status', '<>', '-1')->where('company_id', '3')->get();
        foreach ($site_qa as $qa) {
            //echo $qa->status.' '.$qa->name."<br>";

            $obj_qa = (object) [];
            $obj_qa->id = $qa->id;
            $obj_qa->name = $qa->name;
            $obj_qa->status = $qa->status;
            $obj_qa->super_sign_by = ($qa->supervisor_sign_by) ? User::find($qa->supervisor_sign_by)->fullname : '';
            $obj_qa->super_sign_at = ($qa->supervisor_sign_by) ? $qa->supervisor_sign_at->format('d/m/Y') : '';
            $obj_qa->manager_sign_by = ($qa->manager_sign_by) ? User::find($qa->manager_sign_by)->fullname : '';
            $obj_qa->manager_sign_at = ($qa->manager_sign_by) ? $qa->manager_sign_at->format('d/m/Y') : '';
            $obj_qa->items = [];
            $obj_qa->actions = [];

            // Items
            foreach ($qa->items as $item) {
                $obj_qa->items[$item->order]['id'] = $item->id;
                $obj_qa->items[$item->order]['name'] = $item->name;
                $obj_qa->items[$item->order]['status'] = $item->status;
                $obj_qa->items[$item->order]['done_by'] = '';
                $obj_qa->items[$item->order]['sign_by'] = '';
                $obj_qa->items[$item->order]['sign_at'] = '';

                // Item Completed + Signed Off
                if ($item->status == '1') {
                    $user_signed = User::find($item->sign_by);
                    $company = ($item->done_by) ? Company::find($item->done_by) : $user_signed->company;
                    $obj_qa->items[$item->order]['done_by'] = $company->name_alias . " (lic. $company->licence_no)";
                    $obj_qa->items[$item->order]['sign_by'] = User::find($item->sign_by)->fullname;
                    $obj_qa->items[$item->order]['sign_at'] = $item->sign_at->format('d/m/Y');
                }
            }

            // Action
            foreach ($qa->actions as $action) {
                if (!preg_match('/^Moved report to/', $action->action)) {
                    $obj_qa->actions[$action->id]['action'] = $action->action;
                    $obj_qa->actions[$action->id]['created_by'] = User::find($action->created_by)->fullname;
                    $obj_qa->actions[$action->id]['created_at'] = $action->created_at->format('d/m/Y');
                }
            }
            $data[] = $obj_qa;
        }

        //dd($data);

        //return view('pdf/site-qa', compact('site', 'data'));

        $address = $site->address;
        $pdf = PDF::loadView('pdf/site-qa', compact('site', 'data'));
        $pdf->setPaper('a4');
            //->setOption('cover', View::make('pdf/site-qa-cover', compact('site')))
            //->setOption('footer-font-size', '8')
            //->setOption('footer-center', " blah blah<br><br>\n\n")
            //->setOption('footer-left', utf8_decode('Document created ' . date('\ d/m/Y\ ')))
            //->setOption('footer-html', view('pdf/site-qa-footer'), compact('site', 'data'))
            //->setOption('footer-right', utf8_decode('Page [page] / [topage]'));

        if ($request->has('view_pdf'))
            return $pdf->stream();

        //$file = public_path('filebank/company/' . $doc->for_company_id . '/wms/' . $doc->name . ' v' . $doc->version . ' ref-' . $doc->id . ' ' . '.pdf');
        //if (file_exists($file))
        //    unlink($file);
        //$pdf->save($file);
        return $pdf->stream();

        if ($request->has('email_pdf')) {
            /*$file = public_path('filebank/tmp/jobstart-' . Auth::user()->id  . '.pdf');
            if (file_exists($file))
                unlink($file);
            $pdf->save($file);*/

            if ($request->get('email_list')) {
                $email_list = explode(';', $request->get('email_list'));
                $email_list = array_map('trim', $email_list); // trim white spaces

                $data = [
                    'user_fullname'     => Auth::user()->fullname,
                    'user_company_name' => Auth::user()->company->name,
                    'startdata'         => $startdata
                ];
                Mail::send('emails/jobstart', $data, function ($m) use ($email_list, $data) {
                    $user_email = Auth::user()->email;
                    ($user_email) ? $send_from = $user_email : $send_from = 'do-not-reply@safeworksite.net';

                    $m->from($send_from, Auth::user()->fullname);
                    $m->to($email_list);
                    $m->subject('Upcoming Job Start Dates');
                });
                if (count(Mail::failures()) > 0) {
                    foreach (Mail::failures as $email_address)
                        Toastr::error("Failed to send to $email_address");
                } else
                    Toastr::success("Sent email");

                return view('planner/export/qa');
            }
        }
    }


    public function getItems(Request $request, $id)
    {
        //if ($request->ajax()) {

        $qa = SiteQa::findOrFail($id);

        $items = [];
        $users = [];
        $companies = [];
        foreach ($qa->items as $item) {
            $array = [];
            $array['id'] = $item->id;
            $array['order'] = $item->order;
            $array['name'] = $item->name;
            $array['super'] = $item->super;

            // Task Info
            $array['task_id'] = $item->task_id;
            $task = Task::find($item->task_id);
            //$array['task_name'] = $task->name;
            $array['task_code'] = $task->code;

            // Done By
            if ($item->done_by)
                $array['done_by'] = $item->done_by;
            else {
                // Check Planner which company did the task
                $planned_task = SitePlanner::where('site_id', $qa->site_id)->where('task_id', $item->task_id)->first();
                if ($planned_task && $planned_task->entity_type == 'c' && !$item->super)
                    $array['done_by'] = $planned_task->entity_id;
                else
                    $array['done_by'] = '';
            }

            $array['done_by_company'] = '';
            $array['done_by_licence'] = '';
            if ($array['done_by']) {
                // Company Info - Array of unique companies (store previous companies to speed up)
                if (isset($companies[$item->done_by])) {
                    $company = $companies[$array['done_by']];
                } else {
                    $company = Company::find($array['done_by']);
                    $companies[$array['done_by']] = (object) ['id' => $company->id, 'name_alias' => $company->name_alias, 'licence_no' => $company->licence_no];
                }
                //$array['done_by'] = $item->done_by;
                $array['done_by_company'] = $company->name_alias;
                $array['done_by_licence'] = $company->licence_no;
            }


            // Signed By
            $array['sign_at'] = '';
            $array['sign_by'] = '';
            $array['sign_by_name'] = '';
            if ($item->sign_by) {
                // User Info - Array of unique users (store previous users to speed up)
                if (isset($users[$item->sign_by])) {
                    $user = $users[$item->sign_by];
                } else {
                    $user = User::find($item->sign_by);
                    $users[$item->sign_by] = (object) ['id' => $user->id, 'full_name' => $user->full_name];
                }

                $array['sign_at'] = $item->sign_at->format('Y-m-d');
                $array['sign_by'] = $user->id;
                $array['sign_by_name'] = $user->full_name;
            }
            $array['status'] = $item->status;
            $items[] = $array;
        };


        $actions = [];
        $actions[] = ['value' => '', 'text' => 'Select Action'];
        $actions[] = ['value' => '1', 'text' => 'Sign Off'];
        $actions[] = ['value' => '-1', 'text' => 'Mark N/A'];
        $actions2[] = ['value' => '', 'text' => 'Select Action'];
        $actions2[] = ['value' => '-1', 'text' => 'Mark N/A'];

        $json = [];
        $json[] = $items;
        $json[] = $actions;
        $json[] = $actions2;

        return $json;
        //}
    }

    /**
     * Get Companies with that can do Specific Task
     */
    public function getCompaniesForTask(Request $request, $task_id)
    {
        $trade_id = Task::find($task_id)->trade_id;
        $company_list = Auth::user()->company->companies('1')->pluck('id')->toArray();
        $companies = Company::select(['companys.id', 'companys.name', 'companys.licence_no'])->join('company_trade', 'companys.id', '=', 'company_trade.company_id')
            ->where('companys.status', '1')->where('company_trade.trade_id', $trade_id)
            ->whereIn('companys.id', $company_list)->orderBy('name')->get();

        $array = [];
        $array[] = ['value' => '', 'text' => 'Select company'];
        // Create array in specific Vuejs 'select' format.
        foreach ($companies as $company) {
            $array[] = ['value' => $company->id, 'text' => $company->name_alias, 'licence' => $company->licence_no];
        }

        return $array;
    }

}
