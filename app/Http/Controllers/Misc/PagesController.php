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

class PagesController extends Controller {

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
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $worksite = '';

        // If Site login show check-in form
        if (Session::has('siteID')) {
            $worksite = Site::where('code', Session::get('siteID'))->first();
            if (!$worksite->isUserOnsite(Auth::user()->id)) {
                // Check if User is of a special trade  ie Certifier
                $special_trade_ids = ['19'];  // 19 - Certifier
                if (count(array_intersect(Auth::user()->company->tradesSkilledIn->pluck('id')->toArray(), $special_trade_ids)) > 0) {
                    if (Auth::user()->company->tradesSkilledIn->count() == 1) {
                        // User only has 1 trade which is classified as a 'special' trade
                        return view('site/checkinTrade', compact('worksite'));
                    } else {
                        // User has multiple trades so determine what trade they are loggin as today
                    }
                }

                if ($worksite->id == 254) // Truck
                    return view('site/checkinTruck', compact('worksite'));
                if ($worksite->id == 25) // Store
                    return view('site/checkinStore', compact('worksite'));

                return view('site/checkin', compact('worksite'));
            }
        }

        // Auto redirect to password reset if flagged
        if (Auth::user()->password_reset)
            return redirect('/user/' . Auth::user()->id . '/edit');

        // If primary user and incompleted company Signup - redirect to correct step
        if (Auth::user()->company->status == 2 and Auth::user()->company->primary_user == Auth::user()->id) {
            if (Auth::user()->company->signup_step == 2) $url = '/signup/company/';
            if (Auth::user()->company->signup_step == 3) $url = '/signup/workers/';
            if (Auth::user()->company->signup_step == 4) $url = '/signup/summary/';

            return redirect($url . Auth::user()->company->id);
        }

        return view('pages/home', compact('worksite'));
    }

    public function testcal(Request $request)
    {
        return view('pages/testcal');
    }

    public function reports()
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

    public function licenceOverride(Request $request)
    {
        $companies = \App\Models\Company\Company::where('parent_company', Auth::user()->company_id)->where('status', '1')->orderBy('name')->get();

        return view('manage/report/licence_override', compact('companies'));
    }

    public function settings()
    {
        return view('manage/settings/list');
    }

    public function companyUsers(Request $request)
    {
        $companies_allowed = Auth::user()->company->companies(1)->pluck('id')->toArray();
        $all_companies = \App\Models\Company\Company::where('status', '1')->whereIn('id', $companies_allowed)->orderBy('name')->get();
        $companies_list = DB::table('companys as c')->select(['c.id', 'c.name', 'u.company_id', 'c.updated_at', DB::raw('count(*) as users')])
            ->join('users as u', 'c.id', '=', 'u.company_id')
            ->where('u.status', '1')->whereIn('c.id', $companies_allowed)
            ->groupBy('u.company_id')->orderBy('users')->orderBy('name')->get();

        //dd($companies_list);
        $user_companies = [];
        foreach ($companies_list as $c) {
            $company = \App\Models\Company\Company::find($c->id);

            $user_companies[] = (object) ['id'  => $company->id, 'name' => $company->name_both, 'users' => $c->users,
                                          'sec' => $company->securityUsers(1)->count(), 'pu' => $company->primary_user, 'su' => $company->secondary_user, 'updated_at' => $company->updated_at->format('d/m/Y')];

        }

        //dd($user_companies);

        return view('manage/report/company_users', compact('all_companies', 'user_companies'));
    }


    public function quick(Request $request)
    {

        echo "Todo assigned to inactive user<br><br>";
        $docs = \App\Models\Comms\Todo::all();
        foreach ($docs as $doc) {
            if ($doc->status) {
                foreach ($doc->users as $user) {
                    $u = User::find($user->user_id);
                    if (!$u->status)
                        echo "ToDo [$doc->id] - $doc->name ($u->fullname)<br>";
                }
            }
        }

        echo "<br>Reset Company Licence Override to 0<br><br>";
        $docs = \App\Models\Company\Company::all();
        foreach ($docs as $doc) {
            $doc->lic_override = 0;
            $doc->save();
        }
        /*echo "Child Company LH default permissions<br><br>";
        $lh =  DB::table('role_user')->where('role_id', 12)->get();
        foreach ($lh as $u) {
            $user = User::find($u->user_id);
            echo "$user->fullname<br>";
            $user->attachPermission2(1, 99, $user->company_id);
            $user->attachPermission2(3, 99, $user->company_id);
            $user->attachPermission2(5, 1, $user->company_id);
            $user->attachPermission2(7, 1, $user->company_id);
            $user->attachPermission2(241, 1, $user->company_id);
            $user->attachPermission2(9, 99, $user->company_id);
            $user->attachPermission2(11, 99, $user->company_id);
        }
        echo "Child Company CA default permissions<br><br>";
        $ca =  DB::table('role_user')->where('role_id', 13)->get();
        foreach ($ca as $u) {
            $user = User::find($u->user_id);
            echo "$user->fullname<br>";
            $user->attachPermission2(1, 99, $user->company_id);
            $user->attachPermission2(3, 99, $user->company_id);
            $user->attachPermission2(5, 1, $user->company_id);
            $user->attachPermission2(7, 1, $user->company_id);
            $user->attachPermission2(241, 1, $user->company_id);
            $user->attachPermission2(9, 99, $user->company_id);
            $user->attachPermission2(11, 99, $user->company_id);
        }
        echo "Child Company Tradie default permissions<br><br>";
        $ca =  DB::table('role_user')->where('role_id', 14)->get();
        foreach ($ca as $u) {
            $user = User::find($u->user_id);
            echo "$user->fullname<br>";
            $user->attachPermission2(9, 99, $user->company_id);
        }*/


        /*echo "Creating Primary + Secondary Users for existing Companies<br><br>";
        $companies = \App\Models\Company\Company::all();
        foreach ($companies as $company) {
            if ($company->staffStatus(1)->count() > 0) {
                echo "<br>$company->name " . count($company->staffStatus(1)) . "/" . count($company->staff) . "<br>---------------------------<br>";

                $lhs = $company->usersWithRole('leading.hand');
                if (count($lhs) > 1) {
                    echo "*********   2+ LH *************<br>";
                    foreach ($lhs as $lh) {
                        $inactive = ($lh->status) ? '' : ' *********** INACTIVE';
                        if ($company->id == 21 && $lh->id == 84) { // Dean Taylor
                            $company->primary_user = $lh->id;
                            echo $lh->fullname . "  => PRIMARY<br>";
                            $company->secondary_user = 83;
                            echo "Ian Taylor  => SECONDARY<br>";
                        } elseif ($company->id == 41 && $lh->id == 59) { // Syd Waster Jamie Ross
                            $company->primary_user = $lh->id;
                            echo $lh->fullname . "  => PRIMARY<br>";
                            $company->secondary_user = 301;
                            echo "David Clark  => SECONDARY<br>";
                        } elseif ($company->id == 61 && $lh->id == 17) { // Palace Painiting
                            $company->primary_user = $lh->id;
                            echo $lh->fullname . "  => PRIMARY<br>";
                            $company->secondary_user = 531;
                            echo "Richard Santosa  => SECONDARY<br>";
                        } elseif ($company->id == 109 && $lh->id == 272) { // Pegasus Roofing
                            $company->primary_user = $lh->id;
                            echo $lh->fullname . "  => PRIMARY<br>";
                        } elseif ($company->id == 114 && $lh->id == 298) { // Pro-gyp
                            $company->primary_user = $lh->id;
                            echo $lh->fullname . "  => PRIMARY<br>";
                        } elseif ($company->id == 104 && $lh->id == 237) { // Test Company
                            $company->primary_user = $lh->id;
                            echo $lh->fullname . "  => PRIMARY<br>";
                            $company->secondary_user = 204;
                            echo "Robert Moerman  => SECONDARY<br>";
                        } else
                            echo "$lh->fullname $inactive<br>";
                    }
                } elseif (count($lhs) == 1) {
                    echo $lhs[0]->fullname . " => PRIMARY<br>";
                    $company->primary_user = $lhs[0]->id;
                    $cas = $company->usersWithRole('contractor.admin');
                    if (count($cas) > 1) {
                        echo "*********   2+ CA *************<br>";
                    } elseif (count($cas) == 1) {
                        echo $cas[0]->fullname . "  => SECONDARY<br>";
                        $company->secondary_user = $cas[0]->id;
                    }
                }
                //$company->save();

                foreach ($company->staffStatus(1) as $staff) {
                    if ($staff->is('security')) {
                        echo $staff->fullname . " => ADMIN<br>";
                        $staff->security = 1;
                    } else
                        $staff->security = 0;
                    //$staff->save();
                }
            }
        }
        echo "<br><br>Completed<br>-------------<br>";
        */
    }


    public function completedQA(Request $request)
    {
        echo "Closing completed QA ToDos<br><br>";
        $records = \App\Models\Comms\Todo::where('type', 'qa')->where('status', 1)->get();
        foreach ($records as $rec) {
            $qa = \App\Models\Site\SiteQa::find($rec->type_id);
            if ($qa->status == 0 || $qa->status == - 1) {
                echo '[' . $rec->id . '] qaID:' . $rec->type_id . " - " . $qa->status . "<br>";
                $rec->status = 0;
                $rec->save();
            }
        }
        echo "<br><br>Completed<br>-------------<br>";
    }

    public function refreshQA(Request $request)
    {
        echo "Updating Current QA Reports to match new QA template with Supervisor tick<br><br>";
        $items = \App\Models\Site\SiteQaItem::all();
        foreach ($items as $item) {
            if ($item->master_id) {
                $master = \App\Models\Site\SiteQaItem::find($item->master_id);
                $doc = \App\Models\Site\SiteQa::find($item->doc_id);
                $site = \App\Models\Site\Site::find($doc->site_id);

                // Has master + master set to super but current QA item isn'tr
                if ($master && $master->super && !$item->super) {
                    echo "[$item->id] docID:$item->doc_id $doc->name ($site->name)<br> - $item->name<br><br>";
                    $item->super = 1;
                    if ($item->done_by)
                        $item->done_by = 0;
                    $item->save();
                }

                if (!$item->super) {
                    $doc_master_item = \App\Models\Site\SiteQaItem::where('doc_id', $doc->master_id)->where('task_id', $item->task_id)
                        ->where('name', $item->name)->where('super', '1')->first();
                    if ($doc_master_item) {
                        echo "*[$item->id] docID:$item->doc_id $doc->name ($site->name)<br> - $item->name<br><br>";
                        $item->super = 1;
                        if ($item->done_by)
                            $item->done_by = 0;
                        $item->save();
                    }
                }
            }
        }
        echo "<br><br>Completed<br>-------------<br>";
    }

    public function importCompany(Request $request)
    {
        echo "Importing Companies<br><br>";
        $row = 0;
        if (($handle = fopen(public_path("company.csv"), "r")) !== false) {
            while (($data = fgetcsv($handle, 5000, ",")) !== false) {
                $row ++;
                if ($row == 1) continue;
                $num = count($data);

                $company = \App\Models\Company\Company::find($data[0]);
                if ($company && !($company->id == 120 || $company->id == 121)) {
                    $company->name = $data[1];
                    $company->nickname = $data[2];
                    $company->email = $data[3];
                    $company->phone = $data[4];
                    $company->address = $data[5];
                    $company->suburb = $data[6];
                    $company->state = $data[7];
                    $company->postcode = $data[8];
                    $company->abn = $data[9];
                    $company->gst = $data[10];
                    $company->payroll_tax = $data[11];
                    $company->creditor_code = $data[12];
                    $company->business_entity = $data[13];
                    $company->sub_group = $data[14];
                    $company->category = $data[15];
                    $company->lic_override = $data[16];
                    $company->maxjobs = $data[17];
                    $company->transient = $data[18];
                    $company->primary_user = $data[19];
                    $company->secondary_user = $data[20];

                    $company->status = 0;
                    //$company->approved_by = 424;
                    //$company->approved_at = Carbon::now();
                    echo "<h1>$company->name</h1>";
                    dd($company);
                    //print_r($company);
                    $company->save();

                    /*for ($c = 0; $c < $num; $c ++) {
                        echo $data[$c] . "<br>";
                    }*/
                } elseif ($data[0]) {
                    /*
                    echo "NEW $data[0]<br>";
                    $address = $suburb = $state = $postcode = '';
                    $addy = explode(',', $data[9]);
                    if ($data[9] && count($addy) == 4)
                        list($address, $suburb, $state, $postcode) = explode(',', $data[9]);
                    elseif (($data[9] && count($addy) > 1))
                        echo "<br>***" . count($addy) . '***';
                    // Create Company
                    $company_request = [
                        'name'            => $data[0],
                        'category'        => $data[1],
                        'creditor_code'   => $data[2],
                        'business_entity' => $data[6],
                        'sub_group'       => $data[7],
                        'abn'             => $data[8],
                        'address'         => $address,
                        'suburb'          => $suburb,
                        'state'           => $state,
                        'postcode'        => $postcode,
                        'email'           => $data[10],
                        'gst'             => ($data[17] == 'YES') ? 1 : 0,
                        'payroll_tax'     => $data[23][0],
                        'licence_expiry'  => null,
                        'parent_company'  => 3,

                    ];
                    $company_request['licence_no'] = ($data[33] && $data[33] != 'N/A') ? $data[33] : '';
                    if ($data[34] && preg_match('/\d+\/\d+\/\d+/', $data[34]))
                        $company_request['licence_expiry'] = Carbon::createFromFormat('d/m/Y H:i', $data[34] . '00:00')->toDateTimeString();
                    var_dump($company_request);

                    $newCompany = \App\Models\Company\Company::create($company_request);
                    */
                }

            }
            fclose($handle);
        }
        echo "<br><br>Completed<br>-------------<br>";
    }

    public function createPermission(Request $request)
    {
        //
        // Creating Permission
        //
        $name = 'Company Leave';
        $slug = 'company.leave';
        echo "Creating Permission for $name ($slug)<br><br>";
        // View
        $p = Permission2::create(['name' => "View $name", 'slug' => "view.$slug"]);
        $p->model = 'c';
        $p->save();
        // Edit
        $p = Permission2::create(['name' => "Edit $name", 'slug' => "edit.$slug"]);
        $p->model = 'c';
        $p->save();
        // Add
        $p = Permission2::create(['name' => "Add $name", 'slug' => "add.$slug"]);
        $p->model = 'c';
        $p->save();
        // Delete
        $p = Permission2::create(['name' => "Delete $name", 'slug' => "del.$slug"]);
        $p->model = 'c';
        $p->save();
        // Sig
        $p = Permission2::create(['name' => "Sign Off $name", 'slug' => "sig.$slug"]);
        $p->model = 'c';
        $p->save();
        echo "<br><br>Completed<br>-------------<br>";
    }

    public function fixplanner(Request $request)
    {
        set_time_limit(120);

        //
        // Sites Without Start Dates
        //
        $sites = \App\Models\Site\Site::where('status', '1')->orderBy('name')->get();
        $startJobIDs = \App\Models\Site\Planner\Task::where('code', 'START')->where('status', '1')->pluck('id')->toArray();
        $array = [];
        // Create array in specific Vuejs 'select' format.
        foreach ($sites as $site) {
            $planner = \App\Models\Site\Planner\SitePlanner::where('site_id', $site->id)->orderBy('from')->get();

            $found = false;
            foreach ($planner as $plan) {
                if (in_array($plan->task_id, $startJobIDs)) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $tasks = '0';
                $planner2 = \App\Models\Site\Planner\SitePlanner::where('site_id', $site->id)->get();
                if ($planner2)
                    $tasks = $planner2->count();

                $array[] = ['id' => $site->id, 'code' => $site->code, 'name' => $site->name, 'tasks' => $tasks];
            }
        }

        echo "Sites without START JOB but have other tasks on planner<br><br>";
        foreach ($array as $a) {
            if ($a['tasks'] != 0)
                echo "$a[code] $a[name] - tasks($a[tasks])<br>";
        }

        echo "<br><br>Sites without START JOB but are blank<br><br>";
        foreach ($array as $a) {
            if ($a['tasks'] == 0)
                echo "$a[code] $a[name]<br>";
        }

        echo "<br><br>Completed<br>-------------<br>";

        //
        // Tasks that end before they start
        //
        echo "<br><br>Tasks that end before they start<br><br>";

        $recs = \App\Models\Site\Planner\SitePlanner::orderBy('site_id')->get();
        $count = 0;
        $start = 0;
        foreach ($recs as $rec) {
            if ($rec->to->lt($rec->from)) {
                $site = \App\Models\Site\Site::find($rec->site_id);
                $task = \App\Models\Site\Planner\Task::find($rec->task_id);
                echo "$rec->id F:$rec->from  T:$rec->to site:$site->name   task:$task->name<br>";
                $count ++;
                if ($rec->task_id == 11)
                    $start ++;

                $rec->delete();
            }
        }
        echo "<br><br>Completed<br>-------------<br>";
        echo "Found $count records  with $start START JOBS<br>";

        //
        // Tasks that end before they start
        //
        echo "<br><br>Task with an invaild To/From Date + Days count<br><br>";

        $recs = \App\Models\Site\Planner\SitePlanner::orderBy('id')->get();
        $bad_end = 0;
        $bad_daycount = 0;
        foreach ($recs as $rec) {
            $site = \App\Models\Site\Site::find($rec->site_id);
            $task = \App\Models\Site\Planner\Task::find($rec->task_id);
            $taskname = 'NULL';
            if ($task)
                $taskname = $task->name;

            // Task ends before it starts
            if ($rec->to->lt($rec->from)) {
                echo "END $rec->id F:" . $rec->from->format('Y-m-d') . " T:" . $rec->to->format('Y-m-d') . " site:$site->name   task:$taskname<br>";
                $bad_end ++;
                //$rec->delete(); // delete bad record
            } else {
                $workdays = $this->workDaysBetween($rec->from, $rec->to);
                if ($workdays != $rec->days) {
                    echo "$workdays/$rec->days $rec->id F:" . $rec->from->format('Y-m-d') . " T:" . $rec->to->format('Y-m-d') . " site:$site->name   task:$taskname<br>";
                    $bad_daycount ++;

                    // Update bad record
                    $rec->days = $workdays;
                    $rec->save();
                }
            }
        }
        echo "<br><br>Completed<br>-------------<br>";
        echo "$bad_end records that end before they start  <br>";
        echo "$bad_daycount records with incorrect day count<br>";

    }

    public function workDaysBetween($from, $to, $debug = false)
    {
        if ($from == $to)
            return 1;

        $counter = 0;
        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $from);
        $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $to);
        while ($startDate->format('Y-m-d') != $endDate->format('Y-m-d')) {
            if ($debug) echo "c:" . $counter . " d:" . $startDate->dayOfWeek . ' ' . $startDate->format('Y-m-d') . '<br>';
            if ($startDate->dayOfWeek > 0 && $startDate->dayOfWeek < 6) {
                $counter ++;
                $startDate->addDay();
            } else if ($startDate->dayOfWeek === 6) { // Skip Sat
                if ($debug) echo "skip sat<br>";
                $startDate->addDay();
            } else if ($startDate->dayOfWeek === 0) { // Skip Sun
                if ($debug) echo "skip sun<br>";
                $startDate->addDay();
            }
        }
        if ($endDate->dayOfWeek > 0 && $endDate->dayOfWeek < 6)
            $counter ++;

        return $counter;
    }
}
