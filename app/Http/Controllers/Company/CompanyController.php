<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use Validator;

use DB;
use Mail;
use Carbon\Carbon;
use App\User;
use App\Models\Company\Company;
use App\Models\Company\CompanyDoc;
use App\Models\Company\CompanyLeave;
use App\Models\Site\Planner\SitePlanner;
use App\Models\Site\Planner\Trade;
use App\Models\Site\Planner\Task;
use App\Http\Requests;
use App\Http\Requests\Company\CompanyRequest;
use App\Http\Utilities\CompanyTypes;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Yajra\Datatables\Datatables;
use nilsenj\Toastr\Facades\Toastr;

/**
 * Class CompaniesController
 * @package App\Http\Controllers
 */
class CompanyController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->hasAnyPermissionType('company'))
            return view('errors/404');

        return view('company/list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.company'))
            return view('errors/404');

        return view('company/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'name'        => 'required',
            'person_name' => 'required',
            'email'       => 'required|email|max:255',
            'category'    => 'required',
        ]);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.company'))
            return view('errors/404');

        // Create Company
        $newCompany = Company::create(request()->all());
        $newCompany->signup_key = $newCompany->id . '-' . md5(uniqid(rand(), true));
        $newCompany->nickname = request('person_name');
        $newCompany->save();

        if (request('trades'))
            $newCompany->tradesSkilledIn()->sync(request('trades'));


        // Mail request to new company
        Mail::to(request('email'))->send(new \App\Mail\Company\CompanyWelcome($newCompany, Auth::user()->company, request('person_name')));
        // Mail notification to parent company
        if ($newCompany->parent_company && $newCompany->reportsTo()->notificationsUsersType('n.company.signup.sent'))
            Mail::to($newCompany->reportsTo()->notificationsUsersType('n.company.signup.sent'))->send(new \App\Mail\Company\CompanyCreated($newCompany));

        Toastr::success("Company signup sent");

        return redirect('company');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $company = Company::findorFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('view.company', $company))
            return view('errors/404');

        return view('company/show', compact('company'));
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function users($id)
    {
        $company = Company::findorFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('view.company', $company))
            return view('errors/404');

        return view('company/users', compact('company'));
    }

    /**
     * Edit the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $company = Company::findorFail($id);

        /// Check authorisation and throw 404 if not
        if (!(Auth::user()->allowed2('edit.company', $company)))
            return view('errors/404');

        return view('company/edit', compact('company'));

    }


    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $company = Company::findorFail($id);

        /// Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.company', $company))
            return view('errors/404');

        // Validate
        $validator = Validator::make(request()->all(), [
            'name'         => 'required',
            'phone'        => 'required',
            'email'        => 'required|email|max:255',
            'address'      => 'required',
            'suburb'       => 'required',
            'state'        => 'required',
            'postcode'     => 'required',
            'primary_user' => 'required',
        ]);

        if ($validator->fails()) {
            $validator->errors()->add('FORM', 'company');

            return back()->withErrors($validator)->withInput();
        }

        $old_status = $company->status;
        $company_request = request()->all();
        //dd($company_request);

        // If updated by Parent Company with 'authorise' permissions update approved fields else reset
        if (Auth::user()->allowed2('sig.company', $company)) {
            $company_request['approved_by'] = Auth::user()->id;
            $company_request['approved_at'] = Carbon::now()->toDateTimeString();
        } else {
            $company_request['approved_by'] = 0;
            $company_request['approved_at'] = null;
            // Email Parent if updated
            if ($company->parent_company && $company->reportsTo()->notificationsUsersType('n.company.updated.details'))
                Mail::to($company->reportsTo()->notificationsUsersType('n.company.updated.details'))->send(new \App\Mail\Company\CompanyUpdatedDetails($company));
        }

        $company->update($company_request);
        Toastr::success("Saved changes");

        if (!$company->status && $old_status) {
            // Company made inactive
            if ($company->parent_company && $company->reportsTo()->notificationsUsersType('n.company.signup.completed'))
                Mail::to($company->reportsTo()->notificationsUsersType('n.company.signup.completed'))->send(new \App\Mail\Company\CompanyArchived($company));
            $company->deactivateAllStaff();
            $company->deleteFromPlanner(Carbon::today());
            CompanyLeave::where('from', '>=', Carbon::today()->toDateTimeString())->where('company_id', $company->id)->delete();  // delete future leave
            Toastr::error("Deactivated Company");
        } elseif ($company->status && !$old_status) {
            Toastr::success("Reactivated Company");
            $primary_user = User::find($company->primary_user);
            if ($primary_user) {
                $primary_user->status = 1;
                $primary_user->save();
                Toastr::success("Reactivated Primary User");
            }
            // Company + Primary User reactivated
            if ($company->parent_company && $company->reportsTo()->notificationsUsersType('n.company.signup.completed'))
                Mail::to($company->reportsTo()->notificationsUsersType('n.company.signup.completed'))->send(new \App\Mail\Company\CompanyActive($company));

        }

        return redirect("company/$company->id");
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateBusiness($id)
    {
        $company = Company::findorFail($id);

        /// Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.company.acc', $company))
            return view('errors/404');

        // Validate
        $validator = Validator::make(request()->all(), ['abn' => 'required',]);
        if ($validator->fails()) {
            $validator->errors()->add('FORM', 'business');

            return back()->withErrors($validator)->withInput();
        }

        $company_request = request()->all();
        //dd($company_request);

        // If updated by Parent Company with 'authorise' permissions update approved fields else reset
        if (Auth::user()->allowed2('sig.company.acc', $company)) {
            $company_request['approved_by'] = Auth::user()->id;
            $company_request['approved_at'] = Carbon::now()->toDateTimeString();
        } else {
            $company_request['approved_by'] = 0;
            $company_request['approved_at'] = null;
            // Email Parent if updated
            if ($company->parent_company && $company->reportsTo()->notificationsUsersType('n.company.updated.business'))
                Mail::to($company->reportsTo()->notificationsUsersType('n.company.updated.business'))->send(new \App\Mail\Company\CompanyUpdatedBusiness($company));
        }

        $company->update($company_request);
        Toastr::success("Saved changes");

        return redirect("company/$company->id");
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateWHS($id)
    {
        $company = Company::findorFail($id);

        /// Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.company.whs', $company))
            return view('errors/404');


        // Validate
        $validator = Validator::make(request()->all(), ['lic_override' => 'required_if:lic_override_tog,1'], ['lic_override.required_if' => 'The reason field is required.']);
        if ($validator->fails()) {
            $validator->errors()->add('FORM', 'whs');
            Toastr::error("Failed to save changes");

            return back()->withErrors($validator)->withInput();
        }

        //dd(request()->all());
        $company->update(request()->all());
        Toastr::success("Saved changes");

        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateConstruction($id)
    {
        $company = Company::findorFail($id);

        /// Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.company.con', $company))
            return view('errors/404');

        // Validate
        $validator = Validator::make(request()->all(), ['supervisors' => 'required_if:transient,1'], ['supervisors.required_if' => 'The supervisor name field is required.']);
        if ($validator->fails()) {
            $validator->errors()->add('FORM', 'construction');
            Toastr::error("Failed to save changes");

            return back()->withErrors($validator)->withInput();
        }

        // Update trades for company
        $old_trades = $company->tradesSkilledIn;
        $new_trades = request('trades');

        $planned_trades = '';
        foreach ($old_trades as $old_trade) {
            if (!$new_trades || !in_array($old_trade->id, $new_trades)) {
                echo "checking trade $old_trade->id<br>";
                // Determine if company on planner for this trade
                $planner = SitePlanner::where('entity_type', 'c')->where('entity_id', $company->id)
                    ->whereIn('task_id', Trade::find($old_trade->id)->tasks->pluck('id')->toArray())
                    ->where('to', '>', Carbon::today()->format('Y-m-d'))->get();

                if ($planner->count()) {
                    $planned_trades .= "'" . Trade::find($old_trade->id)->name . "', ";
                    continue;
                }
            }
        }
        $planned_trades = rtrim($planned_trades, ', ');

        if ($planned_trades) {
            Toastr::error("Company is on planner for removed trade");

            return back()->withErrors(['FORM' => 'construction', 'planned_trades' => "This company is currently on the planner for $planned_trades and MUST be removed first."])->withInput();
        }

        $company->update(request()->all());

        // Attach Supervisors if Transient
        if (request('transient')) {
            $company->supervisedBy()->sync(request('supervisors'));
        } else {
            $company->supervisedBy()->detach();
        }

        // Determine if Licence is required
        $old_licence_overide = $company->lic_override;
        $old_trades_skilled_in = $company->tradesSkilledInSBC();
        if (request('trades')) {
            $company->tradesSkilledIn()->sync(request('trades'));
            $company->lic_override = null;
        } else
            $company->tradesSkilledIn()->detach();

        $company->save();
        Toastr::success("Saved changes");

        // Licence Override field was previous set and trades have now changed
        $company = Company::findorFail($id);
        $new_trades_skilled_in = $company->tradesSkilledInSBC();
        if ($old_licence_overide && $old_trades_skilled_in != $new_trades_skilled_in) {
            // Email Parent if updated
            if ($company->parent_company && $company->reportsTo()->notificationsUsersType('n.company.updated.trades'))
                Mail::to($company->reportsTo()->notificationsUsersType('n.company.updated.trades'))->send(new \App\Mail\Company\CompanyUpdatedTrades($company));
        }

        return redirect("company/$company->id");

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function storeLeave($id)
    {
        $company = Company::findorFail($id);

        /// Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.company.leave', $company))
            return view('errors/404');

        // Validate
        $validator = Validator::make(request()->all(), ['from' => 'required', 'notes' => 'required'], ['from.required' => 'Please specify a date range']);
        if ($validator->fails()) {
            $validator->errors()->add('FORM', 'leave.add');
            Toastr::error("Failed to save leave");

            return back()->withErrors($validator)->withInput();
        }

        // Format date from daterange picker to mysql format
        $leave_request = request()->all();
        $leave_request['company_id'] = $company->id;
        $leave_request['from'] = Carbon::createFromFormat('d/m/Y H:i', request('from') . '00:00')->toDateTimeString();
        $leave_request['to'] = Carbon::createFromFormat('d/m/Y H:i', request('to') . '00:00')->toDateTimeString();

        //dd($leave_request);
        // Create Leave
        CompanyLeave::create($leave_request);
        Toastr::success("Created new leave");

        return redirect("company/$company->id");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateLeave($id)
    {
        $company = Company::findorFail($id);

        /// Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.company.leave', $company))
            return view('errors/404');

        foreach (request()->all() as $key => $val) {
            if (preg_match('/from-/', $key)) {
                list($crap, $leave_id) = explode('-', $key);
                $leave_request['from'] = Carbon::createFromFormat('d/m/Y H:i', request("from-$leave_id") . '00:00')->toDateTimeString();
                $leave_request['to'] = Carbon::createFromFormat('d/m/Y H:i', request("to-$leave_id") . '00:00')->toDateTimeString();
                $leave_request['notes'] = request("notes-$leave_id");
                $leave = CompanyLeave::find($leave_id);
                $leave->update($leave_request);
                Toastr::success("Saved changes");
            }
        }

        return redirect("company/$company->id");
    }

    /**
     * Delete the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyLeave($id, $lid)
    {
        $leave = CompanyLeave::findOrFail($lid);

        // Check authorisation
        if (Auth::user()->allowed2('edit.company.leave', $leave->company)) {
            $leave->delete();
            Toastr::success("Deleted leave");
        } else
            Toastr::error("Failed to delete leave");

        return redirect("company/$leave->company_id");
    }


    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function approveCompany($id, $type)
    {
        $company = Company::findorFail($id);

        $type = ($type == 'com') ? '' : ".$type";

        /// Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2("sig.company$type", $company))
            return view('errors/404');

        $company->approved_by = Auth::user()->id;
        $company->approved_at = Carbon::now()->toDateTimeString();
        $company->save();
        Toastr::success("Approved company");

        return redirect('/company/' . $company->id);

    }

    /**
     * Update the photo on user model resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function updateLogo(CompanyRequest $request, $id)
    {
        $company = Company::findorFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.company', $company))
            return view('errors/404');

        $file = $request->file('logo');
        $path = "filebank/company/" . $company->id;
        $name = "logo." . strtolower($file->getClientOriginalExtension());
        $path_name = $path . '/' . $name;
        $file->move($path, $name);

        Image::make(url($path_name))
            ->fit(740)
            ->save($path_name);

        $company->logo_profile = $path_name;
        $company->save();
        Toastr::success("Saved changes");

        return redirect('/company/' . $company->id . '/settings/logo');
    }

    /**
     * Get Company name from given id.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCompanyName($id)
    {
        $company = Company::findOrFail($id);

        return $company->name_alias;
    }

    /**
     * Get Companies current user is authorised to manage + Process datatables ajax request.
     */
    public function getCompanies(Request $request)
    {
        $companies = [];
        if (Auth::user()->company_id == 2) // Safeworksite Website Owner
            $companies = Company::where('status', $request->get('status'))->get();
        elseif (Auth::user()->hasAnyPermissionType('company'))
            $companies = Auth::user()->authCompanies('view.company', $request->get('status'));

        $dt = Datatables::of($companies)
            ->editColumn('id', function ($company) {
                return ($company->status == 2) ? "<div class='text-center'>$company->id</div>" : "<div class='text-center'><a href='/company/$company->id'><i class='fa fa-search'></i></a></div>";
            })
            ->editColumn('name', function ($company) {
                $name = ($company->nickname) ? "$company->name<br><small class='font-grey-cascade'>$company->nickname</small>" : $company->name;
                if ($company->status == 2) {
                    if ($company->signup_step == 1)
                        $name .= ' &nbsp; <span class="label label-sm label-info">Email sent</span> <a href="/signup/welcome/' . $company->id . '" class="btn btn-outline btn-xs dark">Resend Email ' . $company->email . '</a>';
                    if ($company->signup_step == 2)
                        $name .= ' &nbsp; <span class="label label-sm label-info">Adding company info</span></a>';
                    if ($company->signup_step == 3)
                        $name .= ' &nbsp; <span class="label label-sm label-info">Adding users</span></a>';
                    if ($company->signup_step == 4)
                        $name .= ' &nbsp; <span class="label label-sm label-info">Confirming information</span></a>';
                    if ($company->signup_step == 5)
                        $name .= ' &nbsp; <span class="label label-sm label-info">Uploading documents</span></a>';

                    // Created info
                    $name .= "<br><br>Created: " . $company->created_at->format('d/m/Y') . " (" . User::find($company->created_by)->fullname . ")";
                    $name .= "<br><a href='/signup/cancel/$company->id' class='btn btn-xs dark'>Cancel Sign Up & Delete Company</a>";

                }
                if ($company->transient)
                    $name .= ' &nbsp; <span class="label label-sm label-info">' . $company->supervisedBySBC() . '</span>';
                if (!$company->isCompliant() && $company->status == 1)
                    $name .= ' &nbsp; <span class="label label-sm label-danger">Non Compliant</span>';
                if ($company->status == 1 && $company->reportsTo()->id == Auth::user()->company_id && (!$company->approved_by || CompanyDoc::where('for_company_id', $company->id)->where('status', 2)->count()))
                    $name .= ' &nbsp; <span class="label label-sm label-warning">Pending Approval</span>';

                return $name;
            })
            ->addColumn('trade', function ($company) {
                if (preg_match('/[0-9]/', $company->category))
                    return "<b>" . CompanyTypes::name($company->category) . ":</b></span> " . $company->tradesSkilledInSBC();

                return "<b>" . $company->category . ":</b></span> " . $company->tradesSkilledInSBC();
            })
            ->addColumn('manager', function ($company) {
                return $company->seniorUsersSBC();
            })
            ->rawColumns(['id', 'name', 'trade', 'manager'])
            ->make(true);

        return $dt;
    }

    /**
     * Get Staff for specific company + Process datatables ajax request.
     */
    public function getUsers()
    {
        $company = Company::find(request('company_id'));
        if (request('staff') == 'all' && Auth::user()->isCompany($company->id))
            $user_list = Auth::user()->authUsers('view.user')->pluck('id')->toArray();
        else
            $user_list = $company->staff->pluck('id')->toArray();

        $users = User::select([
            'users.id', 'users.username', 'users.firstname', 'users.lastname', 'users.phone', 'users.email', 'users.company_id', 'users.security', 'users.company_id',
            DB::raw('CONCAT(users.firstname, " ", users.lastname) AS full_name'),
            'companys.name', 'users.address', 'users.last_login', 'users.status'])
            ->join('companys', 'users.company_id', '=', 'companys.id')
            ->whereIn('users.id', $user_list)
            ->where('users.status', request('status'));

        $dt = Datatables::of($users)
            //->filterColumn('full_name', 'whereRaw', "CONCAT(firstname,' ',lastname) like ?", ["%$1%"])
            ->editColumn('id', function ($user) {
                if (Auth::user()->allowed2('view.user', $user))
                    return '<div class="text-center"><a href="/user/' . $user->id . '"><i class="fa fa-search"></i></a></div>';
                return '';
            })
            ->editColumn('full_name', function ($user) {
                $string = $user->firstname . ' ' . $user->lastname;

                if ($user->id == $user->company->primary_user)
                    $string .= " <span class='badge badge-info badge-roundless'>P</span>";
                if ($user->id == $user->company->secondary_user)
                    $string .= " <span class='badge badge-info badge-roundless'>S</span>";
                if ($user->security)
                    $string .= " <span class='badge badge-warning badge-roundless'>Sec</span>";

                return $string;
            })
            ->editColumn('name', function ($user) {
                return '<a href="/company/' . $user->company_id . '">' . $user->company->name . '</a>';
            })
            ->editColumn('phone', function ($user) {
                return '<a href="tel:' . preg_replace("/[^0-9]/", "", $user->phone) . '">' . $user->phone . '</a>';
            })
            ->editColumn('email', function ($user) {
                //return '<a href="mailto:' . $user->email . '">' . '<i class="fa fa-envelope-o"></i>' . '</a>';
                return '<a href="mailto:' . $user->email . '">' . $user->email . '</a>';
            })
            ->editColumn('last_login', function ($user) {
                return ($user->last_login != '-0001-11-30 00:00:00') ? with(new Carbon($user->last_login))->format('d/m/Y') : 'never';
            })
            ->rawColumns(['id', 'full_name', 'name', 'phone', 'email', 'action'])
            ->make(true);

        return $dt;
    }
}
