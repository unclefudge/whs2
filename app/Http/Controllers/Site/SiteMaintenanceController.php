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
use App\Models\Site\SiteMaintenance;
use App\Models\Site\SiteMaintenanceItem;
use App\Models\Site\SiteMaintenanceDoc;
use App\Models\Site\Planner\SitePlanner;
use App\Models\Misc\Action;
use App\Models\Company\Company;
use App\Models\Comms\Todo;
use App\Models\Comms\TodoUser;
use App\Jobs\SiteQaPdf;
use App\Http\Requests;
use App\Http\Requests\Site\SiteQaRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Yajra\Datatables\Datatables;
use nilsenj\Toastr\Facades\Toastr;
use Carbon\Carbon;

/**
 * Class SiteMaintenanceController
 * @package App\Http\Controllers\Site
 */
class SiteMaintenanceController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasAnyPermissionType('site.maintenance'))
            return view('errors/404');

        $under_review = DB::table('site_maintenance AS m')
            ->select(['m.id', 'm.site_id', 'm.super_id', 'm.completed', 'm.warranty', 'm.goodwill', 'm.category_id', 'm.status', 'm.updated_at', 'm.created_at',
                DB::raw('CONCAT(u.firstname, " ", u.lastname) AS super_name'),
                DB::raw('DATE_FORMAT(m.created_at, "%d/%m/%y") AS created_date'),
                DB::raw('DATE_FORMAT(m.completed, "%d/%m/%y") AS completed_date'),
                's.code as sitecode', 's.name as sitename', 'u.firstname as firstname'])
            ->join('sites AS s', 'm.site_id', '=', 's.id')
            ->join('users AS u', 'm.super_id', '=', 'u.id')
            ->where('m.status', 2)->get();

        return view('site/maintenance/list', compact('under_review'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.site.maintenance'))
            return view('errors/404');

        return view('site/maintenance/create');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $main = SiteMaintenance::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('view.site.maintenance', $main))
            return view('errors/404');

        if ($main->status == 2)
            return view('site/maintenance/review', compact('main'));
        else
            return view('site/maintenance/show', compact('main'));
    }

    /**
     * Edit the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $main = SiteMaintenance::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site.maintenance', $main))
            return view('errors/404');

        if ($main->status == 2)
            return view('site/maintenance/review', compact('main'));
        else
            return view('site/maintenance/show', compact('main'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.site.maintenance'))
            return view('errors/404');

        request()->validate(['site_id' => 'required', 'super_id' => 'required'], ['site_id.required' => 'The site field is required.', 'super_id.required' => 'The supervisor field is required.']); // Validate

        $site_id = request('site_id');
        $main_request = request()->except('multifile');
        $main_request['completed'] = (request('completed')) ? Carbon::createFromFormat('d/m/Y H:i', request('completed') . '00:00')->toDateTimeString() : null;
        $main_request['status'] = 2; // set new request to 'Under Review'

        //dd($main_request);
        // Create Maintenance Request
        $newMain = SiteMaintenance::create($main_request);

        // Handle file upload
        if (request('multifile')) {
            $files = request()->file('multifile');
            foreach ($files as $file) {
                $path = "filebank/site/$site_id/maintenance";
                $name = $site_id . '-' . sanitizeFilename(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . strtolower($file->getClientOriginalExtension());

                // Ensure filename is unique by adding counter to similiar filenames
                $count = 1;
                while (file_exists(public_path("$path/$name")))
                    $name = $site_id . '-' . sanitizeFilename(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . $count ++ . '.' . strtolower($file->getClientOriginalExtension());
                $file->move($path, $name);

                $doc_request = request()->only('type', 'site_id');
                $doc_request['name'] = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                // Create SitMaintenanceDoc
                $doc = SiteMaintenanceDoc::create($doc_request);
                $doc->main_id = $newMain->id;
                $doc->type = 'photo';
                $doc->attachment = $name;
                $doc->save();
            }
        }

        $action = Action::create(['action' => "Maintenance Request created by " . Auth::user()->fullname, 'table' => 'site_maintenance', 'table_id' => $newMain->id]);


        // Add Request Items
        $order = 1;
        for ($i = 1; $i <= 25; $i ++) {
            if (request("item$i")) {
                SiteMaintenanceItem::create(['main_id' => $newMain->id, 'name' => request("item$i"), 'order' => $order, 'status' => 0]);
                $order ++;
            }
        }
        //dd($main_request);

        // Update Site Status
        $site = Site::find($site_id);
        $site->status = 2;
        $site->save();

        // Create ToDoo for assignment to Supervisor
        $todo_request = [
            'type'       => 'maintenance',
            'type_id'    => $newMain->id,
            'name'       => 'Site Maintenance Client Request - ' . $site->name,
            'info'       => 'Please review request and assign to supervisor',
            'due_at'     => nextWorkDate(Carbon::today(), '+', 2)->toDateTimeString(),
            'company_id' => $site->owned_by->id,
        ];

        // Create ToDoo and assign to Site Supervisors
        $user_list = [3, 7]; // Fudge + Gary
        $todo = Todo::create($todo_request);
        $todo->assignUsers($user_list);

        Toastr::success("Created Maintenance Request");

        return redirect('/site/maintenance');
    }

    /**
     * Update the specified resource in storage.
     */
    public function review($id)
    {
        $main = SiteMaintenance::findOrFail($id);

        // Check authorisation and throw 404 if not
        //if (!Auth::user()->allowed2('edit.site.maintenance', $main))
        //    return view('errors/404');

        $rules = ['company_id' => 'required', 'visit_date' => 'required'];
        $mesg = ['company_id.required' => 'The assign to field is required', 'visit_date.required' => 'The visit date field is required'];
        request()->validate($rules, $mesg); // Validate
        //dd('here');

        $visit_date = Carbon::createFromFormat('d/m/Y H:i:s', request('visit_date') . ' 00:00:00');
        $main_request = request()->all();

        $company = Company::find(request('company_id'));


        if (!request('visited')) {
            // Add to Client Visit planner
            $newPlanner = SitePlanner::create(array(
                'site_id'     => $main->site_id,
                'from'        => $visit_date->format('Y-m-d') . ' 00:00:00',
                'to'          => $visit_date->format('Y-m-d') . ' 00:00:00',
                'days'        => 1,
                'entity_type' => 'c',
                'entity_id'   => request('company_id'),
                'task_id'     => '524' // Client Visit
            ));
            $action = Action::create(['action' => "$company->name assigned to visit client on " . request('visit_date'), 'table' => 'site_maintenance', 'table_id' => $main->id]);
            Toastr::success("Assigned Request");

            // Delete Todoo
            $main->closeToDo(Auth::user());

        }

        // Update Items
        $order = 1;
        $current_items = $main->items->count();
        for ($i = 1; $i <= 25; $i ++) {
            $item = $main->item($i);
            if (request("item$i")) {
                if ($item) {
                    $item->name = request("item$i");
                    $item->order = $order;
                    $item->save();
                } else
                    SiteMaintenanceItem::create(['main_id' => $main->id, 'name' => request("item$i"), 'order' => $order, 'status' => 0]);
                $order ++;
            } elseif ($item)
                $item->delete();
        }

        if ($current_items != ($order - 1)) // Items updated
            $action = Action::create(['action' => "Items updated by " . Auth::user()->fullname, 'table' => 'site_maintenance', 'table_id' => $main->id]);

        // Status Updated
        if (request('status') == 1)  // Maintenance Request Accepted
            $action = Action::create(['action' => "Maintenance Request approved by " . Auth::user()->fullname, 'table' => 'site_maintenance', 'table_id' => $main->id]);
        elseif (request('status') == - 1)  // Maintenance Request Declined
            $action = Action::create(['action' => "Maintenance Request declined by " . Auth::user()->fullname, 'table' => 'site_maintenance', 'table_id' => $main->id]);

        //dd($main_request);
        Toastr::success("Updated Request");

        $main->update($main_request);

        return (request('status') == 2) ? redirect('site/maintenance/' . $main->id . '/edit') : redirect('site/maintenance/' . $main->id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        $main = SiteMaintenance::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site.maintenance', $main))
            return view('errors/404');

        $rules = ['warranty' => 'required', 'category_id' => 'required'];
        $mesg = ['company_id.required' => 'The assign to field is required', 'visit_date.required' => 'The visit date field is required'];
        //request()->validate($rules, $mesg); // Validate

        $main_request = request()->all();
        //dd($main_request);
        $main->update($main_request);


        Toastr::success("Updated Request");

        return redirect('site/maintenance/' . $main->id);
    }


    /**
     * Update Status the specified resource in storage.
     */
    public function updateReport(Request $request, $id)
    {
        $main = SiteMaintenance::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site.maintenance', $main))
            return view('errors/404');

        // Only Allow Ajax requests
        if ($request->ajax()) {
            $main_request = $request->all();

            // Determine if report being signed off
            $signoff = $request->get('signoff');
            if ($signoff == 'super') {
                $main_request['supervisor_sign_by'] = Auth::user()->id;
                $main_request['supervisor_sign_at'] = Carbon::now();

                // Close any outstanding ToDos for supervisors and Create one for Area Super / Con Mgr
                $main->closeToDo(Auth::user());
                if (!$main->manager_sign_by) {
                    $site = Site::findOrFail($main->site_id);
                    $main->createManagerSignOffToDo($site->areaSupervisors()->pluck('id')->toArray());
                }
            }
            if ($signoff == 'manager') {
                $main_request['manager_sign_by'] = Auth::user()->id;
                $main_request['manager_sign_at'] = Carbon::now();
                // Close any outstanding ToDos for Area Super / Con Mgr
                $main->closeToDo(Auth::user());
            }

            // If report was placed On Hold then auto add an Action + close ToDoo
            //if ($request->get('status') == 2 && $main->status != 2)
            //    $main->moveToHold(Auth::user());

            // If report was reactived then auto add an Action + create ToDoo
            //if ($request->get('status') == 1 && $main->status != 1)
            //    $main->moveToActive(Auth::user());

            // If report was marked Not Required then close ToDoo
            //if ($request->get('status') == - 1)
            //    $main->closeToDo(Auth::user());

            $main->update($main_request);

            // Determine if Report Signed Off and if so mark completed
            if ($main->supervisor_sign_by && $main->manager_sign_by) {
                $main->status = 0;
                $main->save();
            }
            Toastr::success("Updated Report");

            return $main;
        }

        return view('errors/404');
    }

    /**
     * Update Item the specified resource in storage.
     *
     */
    public function updateItem(Request $request, $id)
    {
        $item = SiteMaintenanceItem::findOrFail($id);
        $main = SiteMaintenance::findOrFail($item->main_id);
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.site.maintenance', $main))
            return view('errors/404');

        $item_request = $request->only(['status', 'done_by', 'sign_by']);
        //dd($item_request);

        // Update resolve date if just modified
        if (!request('status')) {
            $item->status = 0;
            $item->done_by = null;
            $item->done_at = null;
            $item->save();
        } else {
            // Item completed
            if ($item_request['status'] == 1 && $item->status != 1) {
                $item_request['done_by'] = Auth::user()->id;
                $item_request['done_at'] = Carbon::now()->toDateTimeString();
            }
            // Item signed off
            if ($item_request['sign_by'] && !$item->sign_by) {
                $item_request['sign_by'] = Auth::user()->id;
                $item_request['sign_at'] = Carbon::now()->toDateTimeString();
            }
            // item marked incomplete
            if (!$item_request['sign_by'] && $item->sign_by) {
                $item_request['sign_by'] = null;
                $item_request['sign_at'] = null;
            }
            //dd($item_request);
            $item->update($item_request);
        }

        // Update modified timestamp on QA Doc
        $main = SiteMaintenance::findOrFail($item->main_id);
        $main->touch();

        Toastr::success("Updated record");

        return $item;
    }

    /**
     * Get Prac Completion date.
     */
    public function getPracCompletion()
    {
        $completed = SitePlanner::where('site_id', request('site_id'))->where('task_id', 265)->get()->last();
        if ($completed) {
            //return ($completed->to->format('d/m/Y'));
            return $completed->to;
        }

        return '';
    }

    /**
     * Get Site Supervisor.
     */
    public function getSiteSupervisor()
    {
        $site = Site::find(request('site_id'));

        return ($site) ? $site->supervisors->first()->id : '';
    }

    /**
     * Upload File + Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadAttachment(Request $request)
    {
        // Check authorisation and throw 404 if not
        //if (!(Auth::user()->allowed2('add.safety.doc') || Auth::user()->allowed2('add.site.doc')))
        //    return json_encode("failed");

        // Handle file upload
        $files = $request->file('multifile');
        foreach ($files as $file) {
            $path = "filebank/site/" . $request->get('site_id') . '/maintenance';
            $name = $request->get('site_id') . '-' . sanitizeFilename(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . strtolower($file->getClientOriginalExtension());

            // Ensure filename is unique by adding counter to similiar filenames
            $count = 1;
            while (file_exists(public_path("$path/$name")))
                $name = $request->get('site_id') . '-' . sanitizeFilename(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . $count ++ . '.' . strtolower($file->getClientOriginalExtension());
            $file->move($path, $name);

            $doc_request = $request->only('type', 'site_id');
            $doc_request['name'] = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $doc_request['company_id'] = Auth::user()->company_id;

            // Create SitMaintenanceDoc
            $doc = SiteMaintenanceDoc::create($doc_request);
            $doc->main_id = $this->id;
            $doc->type = 'photo';
            $doc->attachment = $name;
            $doc->save();
        }


        return json_encode("success");
    }


    /**
     * Get QA Reports current user is authorised to manage + Process datatables ajax request.
     */
    public function getMaintenance()
    {
        $records = DB::table('site_maintenance AS m')
            ->select(['m.id', 'm.site_id', 'm.super_id', 'm.completed', 'm.warranty', 'm.goodwill', 'm.category_id', 'm.status', 'm.updated_at', 'm.created_at',
                DB::raw('CONCAT(u.firstname, " ", u.lastname) AS super_name'),
                DB::raw('DATE_FORMAT(m.created_at, "%d/%m/%y") AS created_date'),
                DB::raw('DATE_FORMAT(m.completed, "%d/%m/%y") AS completed_date'),
                's.code as sitecode', 's.name as sitename', 'u.firstname as firstname'])
            ->join('sites AS s', 'm.site_id', '=', 's.id')
            ->join('users AS u', 'm.super_id', '=', 'u.id')
            ->where('m.status', request('status'));

        //dd($records);
        $dt = Datatables::of($records)
            ->editColumn('id', '<div class="text-center"><a href="/site/maintenance/{{$id}}"><i class="fa fa-search"></i></a></div>')
            ->editColumn('site_id', function ($doc) {
                return $doc->sitecode;
            })
            ->editColumn('sitename', function ($doc) {
                return $doc->sitename;
            })
            ->editColumn('supervisor', function ($doc) {
                return $doc->super_name;
            })
            ->addColumn('completed', function ($doc) {
                $main = SiteMaintenance::find($doc->id);
                $total = $main->items()->count();
                $completed = $main->itemsCompleted()->count();
                $pending = '';
                if ($main->status != 0) {
                    if (Auth::user()->allowed2('edit.site.maintenance', $main)) {
                        if ($total == $completed && $total != 0) {
                            $label_type = ($main->supervisor_sign_by && $main->manager_sign_by) ? 'label-success' : 'label-warning';
                            if (!$main->supervisor_sign_by)
                                $pending = '<br><span class="badge badge-info badge-roundless pull-right">Pending Supervisor</span>';
                            elseif (!$main->manager_sign_by)
                                $pending = '<br><span class="badge badge-primary badge-roundless pull-right">Pending Manager</span>';
                        } else
                            $label_type = 'label-danger';

                        return '<span class="label pull-right ' . $label_type . '">' . $completed . ' / ' . $total . '</span>' . $pending;
                    }
                }

                return '<span class="label pull-right label-success">' . $completed . ' / ' . $total . '</span>';
            })
            ->addColumn('action', function ($doc) {
                if (($doc->status && Auth::user()->allowed2('edit.site.maintenance', $doc)) || (!$doc->status && Auth::user()->allowed2('sig.site.maintenance', $doc)))
                    return '<a href="/site/maintenance/' . $doc->id . '" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom"><i class="fa fa-pencil"></i> Edit</a>';

                return '<a href="/site/maintenance/' . $doc->id . '" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom"><i class="fa fa-search"></i> View</a>';

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
        $site = Site::find(request('site_id'));
        if ($site) {
            $completed = 1;
            $data = [];
            $users = [];
            $companies = [];
            $site_qa = SiteQa::where('site_id', $site->id)->where('status', '<>', '-1')->where('company_id', '3')->get();
            foreach ($site_qa as $qa) {
                $obj_qa = (object) [];
                $obj_qa->id = $qa->id;
                $obj_qa->name = $qa->name;
                $obj_qa->status = $qa->status;
                // Signed By Super
                $obj_qa->super_sign_by = '';
                if ($qa->supervisor_sign_by) {
                    if (!isset($users[$qa->supervisor_sign_by]))
                        $users[$qa->supervisor_sign_by] = User::find($qa->supervisor_sign_by);
                    $obj_qa->super_sign_by = $users[$qa->supervisor_sign_by]->fullname;
                } else
                    $completed = 0;
                $obj_qa->super_sign_at = ($qa->supervisor_sign_by) ? $qa->supervisor_sign_at->format('d/m/Y') : '';
                // Signed By Manager
                $obj_qa->manager_sign_by = '';
                if ($qa->manager_sign_by) {
                    if (!isset($users[$qa->manager_sign_by]))
                        $users[$qa->manager_sign_by] = User::find($qa->manager_sign_by);
                    $obj_qa->manager_sign_by = $users[$qa->manager_sign_by]->fullname;
                } else
                    $completed = 0;
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
                        // Get User Signed
                        if (!isset($users[$item->sign_by]))
                            $users[$item->sign_by] = User::find($item->sign_by);
                        $user_signed = $users[$item->sign_by];
                        // Get Company
                        $company = $user_signed->company;
                        if ($item->done_by) {
                            if (!isset($companies[$item->done_by]))
                                $companies[$item->done_by] = Company::find($item->done_by);
                            $company = $companies[$item->done_by];
                        }
                        $obj_qa->items[$item->order]['done_by'] = $company->name_alias . " (lic. $company->licence_no)";
                        $obj_qa->items[$item->order]['sign_by'] = $user_signed->fullname;
                        $obj_qa->items[$item->order]['sign_at'] = $item->sign_at->format('d/m/Y');
                    }
                }

                // Action
                foreach ($qa->actions as $action) {
                    if (!preg_match('/^Moved report to/', $action->action)) {
                        $obj_qa->actions[$action->id]['action'] = $action->action;
                        if (!isset($users[$action->created_by]))
                            $users[$action->created_by] = User::find($action->created_by);
                        $obj_qa->actions[$action->id]['created_by'] = $users[$action->created_by]->fullname;
                        $obj_qa->actions[$action->id]['created_at'] = $action->created_at->format('d/m/Y');
                    }
                }
                $data[] = $obj_qa;
            }

            //dd($data);
            $dir = '/filebank/tmp/report/' . Auth::user()->company_id;
            // Create directory if required
            if (!is_dir(public_path($dir)))
                mkdir(public_path($dir), 0777, true);
            $output_file = public_path($dir . '/QA ' . sanitizeFilename($site->name) . ' (' . $site->id . ') ' . Carbon::now()->format('YmdHis') . '.pdf');
            touch($output_file);

            //return view('pdf/site-qa', compact('site', 'data'));
            //return PDF::loadView('pdf/site-qa', compact('site', 'data'))->setPaper('a4')->stream();
            // Queue the job to generate PDF
            SiteQaPdf::dispatch(request('site_id'), $data, $output_file);
        }

        return redirect('/manage/report/recent');

        if ($request->has('email_pdf')) {
            /*$file = public_path('filebank/tmp/jobstart-' . Auth::user()->id  . '.pdf');
            if (file_exists($file))
                unlink($file);
            $pdf->save($file);*/

            if ($request->get('email_list')) {
                $email_list = explode(';', $request->get('email_list'));
                $email_list = array_map('trim', $email_list); // trim white spaces


                return view('planner/export/qa');
            }
        }
    }


    public function getItems(Request $request, $id)
    {
        //if ($request->ajax()) {

        $main = SiteMaintenance::findOrFail($id);

        $items = [];
        $users = [];
        $companies = [];
        foreach ($main->items as $item) {
            $array = [];
            $array['id'] = $item->id;
            $array['order'] = $item->order;
            $array['name'] = $item->name;
            $array['super'] = $item->super;

            // Task Info
            //$array['task_id'] = $item->task_id;
            //$task = Task::find($item->task_id);
            //$array['task_name'] = $task->name;
            //$array['task_code'] = $task->code;


            // Done By
            $array['done_at'] = '';
            $array['done_by'] = '';
            $array['done_by_name'] = '';
            $array['done_by_company'] = '';
            $array['done_by_licence'] = '';
            if ($item->done_by) {
                // User Info - Array of unique users (store previous users to speed up)
                if (isset($users[$item->done_by])) {
                    $user_rec = $users[$item->done_by];
                } else {
                    $user = User::find($item->done_by);
                    $users[$item->done_by] = (object) ['id' => $user->id, 'full_name' => $user->full_name, 'company_name' => $user->company->name_alias];
                    $user_rec = $users[$item->done_by];
                }

                $array['done_at'] = $item->done_at->format('Y-m-d');
                $array['done_by'] = $user_rec->id;
                $array['done_by_name'] = $user_rec->full_name;
                $array['done_by_company'] = $user_rec->company_name;
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
        $actions[] = ['value' => '1', 'text' => 'Completed'];
        $actions[] = ['value' => '-1', 'text' => 'Mark N/A'];
        $actions2[] = ['value' => '', 'text' => 'Select Action'];
        $actions2[] = ['value' => '0', 'text' => 'Incomplete'];
        $actions2[] = ['value' => '1', 'text' => 'Sign Off'];

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
