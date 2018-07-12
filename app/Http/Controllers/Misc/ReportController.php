<?php

namespace App\Http\Controllers\Misc;

use DB;
use Session;
use App\User;
use App\Models\Site\Site;
use App\Models\Site\Planner\SiteAttendance;
use App\Models\Site\Planner\SiteCompliance;
use App\Models\Site\SiteQa;
use App\Models\Misc\Permission2;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
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

    public function recent()
    {
        return view('manage/report/recent');
    }

    public function recentFiles()
    {
        $dir = '/filebank/tmp/report/' . Auth::user()->company_id;
        // Create directory if required
        if (!is_dir(public_path($dir)))
            mkdir(public_path($dir), 0777, true);

        $files = scandir_datesort(public_path($dir));

        //dd($files);
        $reports = [];
        foreach ($files as $file) {
            if (($file[0] != '.')) {
                $processed = false;
                if (filesize(public_path("$dir/$file")) > 0)
                    $processed = true;

                $date = Carbon::createFromFormat('YmdHis', substr($file, - 18, 4) . substr($file, - 14, 2) . substr($file, - 12, 2) . substr($file, - 10, 2) . substr($file, - 8, 2) . substr($file, - 6, 2));
                $deleted = false;
                if ($date->lt(Carbon::today()->subDays(10))) {
                    unlink(public_path("$dir/$file"));
                    $deleted = true;
                }

                if (!$deleted)
                    $reports[$file] = filesize(public_path("$dir/$file"));

                //$done = substr($file, - 5, 1);
                //preg_match('#\((.*?)\)#', $file, $match);
                //$site_id = $match[1];
                //$site = Site::find($site_id);
            }
        }

        return $reports;

    }

    public function newusers()
    {
        $allowed_users = Auth::user()->company->users(1)->pluck('id')->toArray();
        $users = \App\User::where('created_at', '>', '2016-08-27 12:00:00')->whereIn('id', $allowed_users)->orderBy('created_at', 'DESC')->get();

        return view('manage/report/newusers', compact('users'));
    }

    public function newcompanies()
    {
        $allowed_companies = Auth::user()->company->companies(1)->pluck('id')->toArray();
        $companies = \App\Models\Company\Company::where('created_at', '>', '2016-08-27 12:00:00')->whereIn('id', $allowed_companies)->orderBy('created_at', 'DESC')->get();

        return view('manage/report/newcompanies', compact('companies'));
    }

    public function users_noemail()
    {
        $allowed_users = Auth::user()->company->users(1)->pluck('id')->toArray();
        $users = \App\User::where('email', null)->where('status', 1)->whereIn('id', $allowed_users)->orderBy('company_id', 'ASC')->get();

        return view('manage/report/users_noemail', compact('users'));
    }

    public function roleusers()
    {
        $allowed_users = Auth::user()->company->users(1)->pluck('id')->toArray();
        $users = DB::table('role_user')->whereIn('user_id', $allowed_users)->orderBy('role_id')->get();

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

    public function nightly()
    {
        $files = array_reverse(array_diff(scandir(public_path('/filebank/log/nightly')), array('.', '..')));

        return view('manage/report/nightly', compact('files'));
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

    /*
     * Site Attendance Report
     */
    public function attendance()
    {
        $companies = \App\Models\Company\Company::where('parent_company', Auth::user()->company_id)->where('status', '1')->orderBy('name')->get();

        return view('manage/report/attendance', compact('companies'));
    }

    /*
     * Payroll Report
     */
    public function payroll()
    {
        $companies = \App\Models\Company\Company::where('parent_company', Auth::user()->company_id)->where('status', '1')->orderBy('name')->get();
        $companies = Auth::user()->company->companies();

        return view('manage/report/payroll', compact('companies'));
    }

    /**
     * Get Site Attendance user is authorise to view
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getAttendance()
    {

        $site_id_all = (request('site_id_all') == 'all') ? '' : request('site_id_all');
        $site_id_active = (request('site_id_active') == 'all') ? '' : request('site_id_active');
        $site_id_completed = (request('site_id_completed') == 'all') ? '' : request('site_id_completed');
        $company_id = (request('company_id') == 'all') ? '' : request('company_id');

        if (request('status') == 1)
            $site_ids = ($site_id_active) ? [$site_id_active] : Auth::user()->company->sites(1)->pluck('id')->toArray();
        elseif (request('status') == '0')
            $site_ids = ($site_id_completed) ? [$site_id_completed] : Auth::user()->company->sites(0)->pluck('id')->toArray();
        else
            $site_ids = ($site_id_all) ? [$site_id_all] : Auth::user()->company->sites()->pluck('id')->toArray();

        $date_from = (request('from')) ? Carbon::createFromFormat('d/m/Y H:i:s', request('from') . ' 00:00:00')->format('Y-m-d') : '2000-01-01';
        $date_to = (request('to')) ? Carbon::createFromFormat('d/m/Y H:i:s', request('to') . ' 00:00:00')->format('Y-m-d') : Carbon::tomorrow()->format('Y-m-d');


        //dd(request('site_id_all'));

        $company_ids = ($company_id) ? [$company_id] : Auth::user()->company->companies()->pluck('id')->toArray();

        $attendance_records = SiteAttendance::select([
            'site_attendance.site_id', 'site_attendance.user_id', 'site_attendance.date', 'sites.name',
            'users.id', 'users.username', 'users.firstname', 'users.lastname', 'users.company_id', 'companys.id', 'companys.name',
            DB::raw('CONCAT(users.firstname, " ", users.lastname) AS full_name')
        ])
            ->join('sites', 'sites.id', '=', 'site_attendance.site_id')
            ->join('users', 'users.id', '=', 'site_attendance.user_id')
            ->join('companys', 'users.company_id', '=', 'companys.id')
            ->whereIn('site_attendance.site_id', $site_ids)
            ->whereIn('companys.id', $company_ids)
            ->whereDate('site_attendance.date', '>=', $date_from)
            ->whereDate('site_attendance.date', '<=', $date_to);

        //dd($attendance_records);
        $dt = Datatables::of($attendance_records)
            ->editColumn('date', function ($attendance) {
                return $attendance->date->format('d/m/Y H:m a');
            })
            ->editColumn('sites.name', function ($attendance) {
                return '<a href="/site/' . $attendance->site->slug . '">' . $attendance->site->name . '</a>';
            })
            ->editColumn('full_name', function ($attendance) {
                return '<a href="/user/' . $attendance->user->id . '">' . $attendance->user->full_name . '</a>';
            })
            ->editColumn('companys.name', function ($attendance) {
                return '<a href="/company/' . $attendance->user->company_id . '">' . $attendance->user->company->name . '</a>';
            })
            ->rawColumns(['id', 'full_name', 'companys.name', 'sites.name'])
            ->make(true);

        return $dt;
    }

}
