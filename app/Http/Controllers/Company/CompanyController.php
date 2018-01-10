<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use Validator;

use DB;
use Mail;
use Carbon\Carbon;
use App\User;
use App\Mail\CompanyWelcome;
use App\Models\Company\Company;
use App\Models\Site\Planner\SitePlanner;
use App\Http\Requests;
use App\Http\Requests\Company\CompanyRequest;
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
        ]);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.company') || Auth::user()->company->subscription < 2)
            return view('errors/404');

        //Mail::to($request->get('email'))->send(new CompanyWelcome(Auth::user()->company, Auth::user()->company, $request->get('person_name')));
        //dd($request->all());

        // Create Company
        $newCompany = Company::create(request()->all());
        $newCompany->signup_key = $newCompany->id . '-' . md5(uniqid(rand(), true));
        $newCompany->save();

        // Mail request to new company
        Mail::to(request('email'))->send(new App\Mail\Company\CompanyWelcome($newCompany, Auth::user()->company, request('person_name')));

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
        if (!Auth::user()->allowed2('view.company', $company) || $company->status == 2)
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
            (Auth::user()->hasAnyPermission2('add.trade|edit.trade') && $company->reportsToCompany()->id == Auth::user()->company_id))
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

        // Add Users
        if ($step == 3)
            return view('company/signup-users', compact('company'));

        // Add Documents
        if ($step == 4) {
            // Company added all users so email parent company
            if ($company->parent_company) {
                //Mail::to($company->reportsToCompany()->notificationsUsersType('company.signup'))->send(new CompanyWelcome($newCompany, Auth::user()->company, request('person_name')));
            }
            $company->status = 1;
            $company->signup_step = 4;
            $company->save();

            return redirect("company/$company->id");
        }

        // Signup complete
        if ($step == 5) {
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
    public function update(CompanyRequest $request, $id)
    {
        $company = Company::findorFail($id);

        /// Check authorisation and throw 404 if not
        // User must be able to edit company or has subscription 2+ with ability to edit trades
        if (!(Auth::user()->allowed2('edit.company', $company) || (Auth::user()->company->subscription > 1 &&
                (Auth::user()->hasAnyPermission2('add.trade|edit.trade') && $company->reportsToCompany()->id == Auth::user()->company_id)))
        )
            return view('errors/404');

        $company_request = $request->except('supervisors', 'trades', 'slug');

        // If not Transient set field to 0 and clear supervisors
        if (!$request->has('transient'))
            $company_request['transient'] = '0';

        //$company_request['licence_expiry'] = ($request->get('licence_expiry')) ? Carbon::createFromFormat('d/m/Y H:i', $request->get('licence_expiry') . '00:00')->toDateTimeString() : null;

        // If updated by Parent Company with 'authorise' permissions update approved fields else reset
        if (Auth::user()->allowed2('del.company', $company)) {
            $company_request['approved_by'] = Auth::user()->id;
            $company_request['approved_at'] = Carbon::now()->toDateTimeString();
        } else {
            $company_request['approved_by'] = 0;
            $company_request['approved_at'] = null;
        }

        $company->update($company_request);

        // Update trades + supervisors for company
        // Only updatable by 'parent' company
        if ($company->reportsToCompany()->id == Auth::user()->company_id) {
            if ($request->get('trades'))
                $company->tradesSkilledIn()->sync($request->get('trades'));
            else
                $company->tradesSkilledIn()->detach();

            if ($request->has('transient'))
                $company->supervisedBy()->sync($request->get('supervisors'));
            else
                $company->supervisedBy()->detach();
        }

        // Actions for making company inactive
        // delete future leave
        if (!$company->status) {
            $company->deactivateAllStaff();
            $company->deleteFromPlanner(Carbon::today());
            Toastr::error("Deactivated Company");
        }

        Toastr::success("Saved changes");

        // Signup Process - Initial update
        if ($company->signup_step == 3)
            return redirect("company/$company->id/signup/3");   // Adding users

        return redirect("company/$company->id");
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
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function approveCompany($id)
    {
        $company = Company::findorFail($id);

        /// Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('sig.company', $company))
            return view('errors/404');

        $company->approved_by = Auth::user()->id;
        $company->approved_at = Carbon::now()->toDateTimeString();
        $company->save();
        Toastr::success("Approved company");

        return redirect('/company/' . $company->id);

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
                if ($company->transient)
                    $name .= ' &nbsp; <span class="label label-sm label-info">' . $company->supervisedBySBC() . '</span>';
                if (!$company->approved_by && $company->status == 1 && $company->reportsToCompany()->id == Auth::user()->company_id)
                    $name .= ' &nbsp; <span class="label label-sm label-warning">Pending approval</span>';

                return $name;
            })
            ->addColumn('trade', function ($company) {
                return ($company->category) ? "<b>$company->category:</b></span> " . $company->tradesSkilledInSBC() : $company->tradesSkilledInSBC();
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
                    return '<div class="text-center"><a href="/user/' . $user->username . '"><i class="fa fa-search"></i></a></div>';
            })
            ->rawColumns(['full_name', 'phone', 'email', 'action'])
            ->make(true);

        return $dt;
    }

    /**
     * Overide the default return URL form failed validation request
     * with custom user/{username}/settings/info
     *
     * @param array $errors
     * @return $this|JsonResponse
     */
    public function response(array $errors)
    {
        // Optionally, send a custom response on authorize failure
        // (default is to just redirect to initial page with errors)
        //
        // Can return a response, a view, a redirect, or whatever else

        if ($this->ajax() || $this->wantsJson()) {
            return new JsonResponse($errors, 422);
        }

        $user = User::find($this->get('id'));
        $username = $user->username;
        $tabs = $this->get('tabs');

        switch ($tabs) {
            case 'settings:info':
                return $this->redirector->to('user/' . $username . '/settings/info')
                    ->withInput($this->except($this->dontFlash))
                    ->withErrors($errors, $this->errorBag);
            case 'settings:password':
                return $this->redirector->to('user/' . $username . '/settings/password')
                    ->withInput($this->except($this->dontFlash))
                    ->withErrors($errors, $this->errorBag);
            default:
                return $this->redirector->to($this->getRedirectUrl())
                    ->withInput($this->except($this->dontFlash))
                    ->withErrors($errors, $this->errorBag);
        }
    }
}
