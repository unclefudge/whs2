<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use Validator;

use DB;
use Mail;
use Carbon\Carbon;
use App\User;
use App\Models\Company\Company;
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
        if (!(Auth::user()->company->subscription > 1 && Auth::user()->hasAnyPermissionType('company')))
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
        if (!Auth::user()->allowed2('add.company') || Auth::user()->company->subscription < 2)
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
        if (!Auth::user()->allowed2('add.company') || Auth::user()->company->subscription < 2)
            return view('errors/404');

        // Create Company
        $newCompany = Company::create(request()->all());
        $newCompany->signup_key = $newCompany->id . '-' . md5(uniqid(rand(), true));
        $newCompany->nickname = request('person_name');
        $newCompany->save();

        if (request('trades')) {
            $newCompany->tradesSkilledIn()->sync(request('trades'));
            foreach (request('trades') as $trade) {
                if (Trade::find($trade)->licence_req) {
                    $newCompany->licence_required = 1;
                    $newCompany->save();
                    break;
                }
            }
        }

        // Mail request to new company
        Mail::to(request('email'))->send(new \App\Mail\Company\CompanyWelcome($newCompany, Auth::user()->company, request('person_name')));

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
     * Edit the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $company = Company::findorFail($id);

        /// Check authorisation and throw 404 if not
        // User must be able to edit company or has subscription 3+ with ability to edit trades
        if (!(Auth::user()->allowed2('edit.company', $company) || Auth::user()->company->subscription > 2 &&
            (Auth::user()->hasAnyPermission2('add.trade|edit.trade') && $company->reportsTo()->id == Auth::user()->company_id))
        )
            return view('errors/404');

        return view('company/edit', compact('company'));

    }

    /**
     * Show the form for sign up process step x
     *
     * @return \Illuminate\Http\Response
     */
    public function signupProcess($id, $step)
    {
        $company = Company::findorFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('view.company', $company))
            return view('errors/404');

        // Resend Welcome Email
        if ($step == 1) {
            Mail::to($company)->send(new \App\Mail\Company\CompanyWelcome($company, Auth::user()->company, $company->nickname));

            return view('company/list');
        }

        // Add Users
        if ($step == 3)
            return view('company/signup/users', compact('company'));

        // Summary
        if ($step == 4) {
            $company->signup_step = 4;
            $company->save();

            //dd('here');
            return view("company/signup/summary", compact('company'));
        }

        // Add Documents
        if ($step == 5) {
            $company->signup_step = 5;
            $company->status = 1;
            $email_to = (\App::environment('prod')) ? $company->reportsTo()->notificationsUsersType('company.signup') : env('EMAIL_ME');
            if ($company->parent_company && $email_to)
                Mail::to($email_to)->send(new \App\Mail\Company\CompanyCreated($company));

            $company->save();

            return redirect("company/$company->id");
        }

        // Signup complete
        if ($step == 6) {
            $company->signup_step = 0;
            $company->save();
            Toastr::success("Congratulations! Signup Complete");

            return redirect("company/$company->id");
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $company = Company::findorFail($id);

        /// Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.company', $company))
            return view('errors/404');

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

            //return redirect("company/$company->id")->withErrors($validator)->withInput();
            return back()->withErrors($validator)->withInput();
        }

        $company_request = request()->all();

        //dd($company_request);

        // If updated by Parent Company with 'authorise' permissions update approved fields else reset
        if (Auth::user()->allowed2('sig.company', $company)) {
            $company_request['approved_by'] = Auth::user()->id;
            $company_request['approved_at'] = Carbon::now()->toDateTimeString();
        } else {
            $company_request['approved_by'] = 0;
            $company_request['approved_at'] = null;
        }

        $company->update($company_request);

        // Actions for making company inactive
        // delete future leave
        if (!$company->status) {
            $company->deactivateAllStaff();
            $company->deleteFromPlanner(Carbon::today());
            Toastr::error("Deactivated Company");
        }

        Toastr::success("Saved changes");

        // Signup Process - Initial update
        if ($company->status == 2 && $company->signup_step)
            return redirect("company/$company->id/signup/3");   // Adding users

        return redirect("company/$company->id");
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateBusiness(Request $request, $id)
    {
        $company = Company::findorFail($id);

        /// Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.company.acc', $company))
            return view('errors/404');

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

        $validator = Validator::make(request()->all(), []);

        if ($validator->fails()) {
            $validator->errors()->add('FORM', 'whs');

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
    public function updateTrade(Request $request, $id)
    {
        $company = Company::findorFail($id);

        /// Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.company.con', $company))
            return view('errors/404');

        $validator = Validator::make(request()->all(), ['supervisors' => 'required_if:transient,1'], ['supervisors.required_if' => 'The supervisor name field is required.']);

        if ($validator->fails()) {
            $validator->errors()->add('FORM', 'construction');
            Toastr::error("Failed to save changes");

            return back()->withErrors($validator)->withInput();
        }

        // Update trades for company
        $old_trades = $company->tradesSkilledIn;
        $new_trades = $request->get('trades');

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
        if ($request->get('transient')) {
            $company->supervisedBy()->sync($request->get('supervisors'));
        } else {
            $company->supervisedBy()->detach();
        }

        // Determine if Licence is required
        $old_licence_overide = ($company->licence_required != $company->requiresContractorsLicence()) ? true : false;
        $old_trades_skilled_in = $company->tradesSkilledInSBC();
        if ($request->get('trades')) {
            $company->tradesSkilledIn()->sync($request->get('trades'));
            $company->licence_required = 0;
            foreach (request('trades') as $trade) {
                if (Trade::find($trade)->licence_req) {
                    $company->licence_required = 1;
                    break;
                }
            }
            // Licence Required field was previous overridden and trades have now changed
            if ($old_licence_overide && $old_trades_skilled_in != $company->tradesSkilledInSBC()) {
                // email tara
            }
        } else
            $company->tradesSkilledIn()->detach();

        $company->save();
        Toastr::success("Saved changes");

        return redirect("company/$company->id");

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
        elseif (Auth::user()->company->subscription > 1 && Auth::user()->hasAnyPermissionType('company'))
            $companies = Auth::user()->authCompanies('view.company', $request->get('status'));

        $dt = Datatables::of($companies)
            ->editColumn('id', function ($company) {
                return ($company->status == 2) ? '' : "<div class='text-center'><a href='/company/$company->id'><i class='fa fa-search'></i></a></div>";
            })
            ->editColumn('name', function ($company) {
                $name = ($company->nickname) ? "$company->name<br><small class='font-grey-cascade'>$company->nickname</small>" : $company->name;
                if ($company->status == 2) {
                    if ($company->signup_step == 1)
                        $name .= ' &nbsp; <span class="label label-sm label-info">Email sent</span> <a href="/company/' . $company->id . '/signup/1" class="btn btn-outline btn-xs dark">Resend Email ' . $company->email . '</a>';
                    if ($company->signup_step == 2)
                        $name .= ' &nbsp; <span class="label label-sm label-info">Adding company info</span></a>';
                    if ($company->signup_step == 3)
                        $name .= ' &nbsp; <span class="label label-sm label-info">Adding users</span></a>';
                    if ($company->signup_step == 4)
                        $name .= ' &nbsp; <span class="label label-sm label-info">Confirming information</span></a>';
                    if ($company->signup_step == 5)
                        $name .= ' &nbsp; <span class="label label-sm label-info">Uploading documents</span></a>';

                }
                if ($company->transient)
                    $name .= ' &nbsp; <span class="label label-sm label-info">' . $company->supervisedBySBC() . '</span>';
                if (!$company->approved_by && $company->status == 1 && $company->reportsTo()->id == Auth::user()->company_id)
                    $name .= ' &nbsp; <span class="label label-sm label-warning">Pending approval</span>';
                if (!$company->compliantDocs() && $company->status == 1)
                    $name .= ' &nbsp; <span class="label label-sm label-danger">Non Compliant</span>';

                return $name;
            })
            ->addColumn('trade', function ($company) {
                if (preg_match('/[0-9]/', $company->category))
                    return "<b>" . CompanyTypes::name($company->category) . ":</b></span> " . $company->tradesSkilledInSBC();

                return "<b>" . $company->category . ":</b></span> " . $company->tradesSkilledInSBC();
                //return "<b>".CompanyTypes::name($company->category).":</b></span> " . $company->tradesSkilledInSBC();
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
    public function getStaff(Request $request)
    {
        $company_id = $request->get('company_id');

        $staff = User::select([
            'id', 'username', 'firstname', 'lastname', 'phone', 'email', 'status', 'company_id',
            DB::raw('CONCAT(firstname, " ", lastname) AS full_name')])
            ->where('company_id', '=', $company_id)
            ->where('status', '=', '1');

        $dt = Datatables::of($staff)
            //->filterColumn('full_name', 'whereRaw', "CONCAT(firstname,' ',lastname) like ?", ["%$1%"])
            ->editColumn('full_name', function ($user) {
                $string = $user->fullname;
                if ($user->id == $user->company->primary_user)
                    $string .= " <span class='badge badge-info badge-roundless'>P</span> ";
                if ($user->id == $user->company->secondary_user)
                    $string .= " <span class='badge badge-info badge-roundless'>S</span> ";
                if ($user->security)
                    $string .= " <span class='badge badge-warning badge-roundless'>Sec</span>";

                return $string;
            })
            ->editColumn('phone', function ($user) {
                return '<a href="tel:' . preg_replace("/[^0-9]/", "", $user->phone) . '">' . $user->phone . '</a>';
            })
            ->editColumn('email', function ($user) {
                return '<a href="mailto:' . $user->email . '">' . $user->email . '</a>';
            })
            ->addColumn('action', function ($user) {
                if (Auth::user()->allowed2('view.user', $user))
                    return '<div class="text-center"><a href="/user/' . $user->id . '"><i class="fa fa-search"></i></a></div>';
            })
            ->rawColumns(['full_name', 'phone', 'email', 'action'])
            ->make(true);

        return $dt;
    }
}
