<?php

namespace App\Http\Controllers\Misc;

use DB;
use Session;
use App\User;
use App\Models\Site\Site;
use App\Models\Site\Planner\SiteCompliance;
use App\Models\Site\SiteQa;
use App\Models\Misc\Permission2;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the report list.
     *
     * @return Response
     */
    public function index()
    {
        return view('manage/report/list');
    }

    public function newusers()
    {
        $users = \App\User::where('created_at', '>', '2016-08-27 12:00:00')->orderBy('created_at', 'DESC')->get();

        return view('manage/report/newusers', compact('users'));
    }

    public function newcompanies()
    {
        $companies = \App\Models\Company\Company::where('created_at', '>', '2016-08-27 12:00:00')->orderBy('created_at', 'DESC')->get();

        return view('manage/report/newcompanies', compact('companies'));
    }

    public function users_noemail()
    {
        $users = \App\User::where('email', null)->where('status', 1)->orderBy('company_id', 'ASC')->get();

        return view('manage/report/users_noemail', compact('users'));
    }

    public function roleusers()
    {
        $users = DB::table('role_user')->orderBy('role_id')->get();

        return view('manage/report/roleusers', compact('users'));
    }

    public function usersExtraPermissions()
    {
        $permissions = DB::table('permission_user')->where('company_id', Auth::user()->company_id)->orderBy('user_id')->get();

        return view('manage/report/users_extra_permissions', compact('permissions'));
    }

    public function missingCompanyInfo()
    {
        $companies = \App\Models\Company\Company::where('parent_company', Auth::user()->company_id)->where('status', '1')->orderBy('name')->get();

        return view('manage/report/missing_company_info', compact('companies'));
    }

    public function licenceOverride()
    {
        $companies = \App\Models\Company\Company::where('parent_company', Auth::user()->company_id)->where('status', '1')->orderBy('name')->get();

        return view('manage/report/licence_override', compact('companies'));
    }

    public function companyUsers()
    {
        $companies_allowed = Auth::user()->company->companies(1)->pluck('id')->toArray();
        $all_companies = \App\Models\Company\Company::where('status', '1')->whereIn('id', $companies_allowed)->orderBy('name')->get();
        $companies_list = DB::table('companys as c')->select(['c.id', 'c.name', 'u.company_id', 'c.updated_at', DB::raw('count(*) as users')])
            ->join('users as u', 'c.id', '=', 'u.company_id')
            ->where('u.status', '1')->whereIn('c.id', $companies_allowed)
            ->groupBy('u.company_id')->orderBy('users')->orderBy('name')->get();

        $user_companies = [];
        foreach ($companies_list as $c) {
            $company = \App\Models\Company\Company::find($c->id);

            $user_companies[] = (object) ['id'  => $company->id, 'name' => $company->name_both, 'users' => $c->users,
                                          'sec' => $company->securityUsers(1)->count(), 'pu' => $company->primary_user, 'su' => $company->secondary_user, 'updated_at' => $company->updated_at->format('d/m/Y')];

        }
        return view('manage/report/company_users', compact('all_companies', 'user_companies'));
    }

    public function nightly()
    {
        $files = array_reverse(array_diff(scandir(public_path('/filebank/log/nightly')), array('.', '..')));

        return view('manage/report/nightly', compact('files'));
    }
}
