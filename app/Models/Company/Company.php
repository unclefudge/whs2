<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\User;
use App\Models\Site\Site;
use App\Models\Site\Planner\SitePlanner;
use App\Models\Site\Planner\Trade;
use App\Models\Site\Planner\Task;
use App\Models\Comms\SafetyTip;
use App\Models\Safety\WmsDoc;
use App\Models\Safety\ToolboxTalk;
use App\Models\Misc\ContractorLicence;
use App\Models\Misc\SettingsNotification;
use App\Models\Misc\Role2;
use App\Models\Company\CompanyDoc;
use App\Http\Utilities\SettingsNotificationTypes;
use Carbon\Carbon;
use nilsenj\Toastr\Facades\Toastr;
use DB;
use Session;

class Company extends Model {

    protected $table = 'companys';
    protected $fillable = [
        'name', 'nickname', 'slug', 'email', 'phone', 'logo_banner', 'logo_profile',
        'address', 'address2', 'suburb', 'state', 'postcode', 'country', 'primary_user', 'secondary_user',
        'abn', 'gst', 'business_entity', 'sub_group', 'payroll_tax', 'creditor_code', 'category',
        'licence_required', 'licence_no', 'licence_type', 'licence_expiry',
        'transient', 'maxjobs', 'notes', 'parent_company', 'subscription', 'signup_key', 'signup_step',
        'status', 'created_by', 'updated_by', 'approved_by', 'approved_at'];

    protected $dates = ['licence_expiry', 'approved_at'];

    /**
     * A Company has many staff.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function staff()
    {
        return $this->hasMany('App\User');
    }

    /**
     * A Company has a Primary Contact
     */
    public function primary_contact()
    {
        if ($this->primary_user)
            return User::findOrFail($this->primary_user);

        return null;
    }

    /**
     * A Company may have a Secondary Contact
     */
    public function secondary_contact()
    {
        if ($this->secondary_user)
            return User::findOrFail($this->secondary_user);

        return null;
    }

    /**
     * A Company has many clients.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clients()
    {
        return $this->hasMany('App\Models\Misc\Client');
    }

    /**
     * A Company was created by a User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * A Company has many CompanyLeave.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function leave()
    {
        return $this->hasMany('App\Models\Company\CompanyLeave');
    }

    /**
     * A dropdown list of clients for the company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clientSelect($prompt = '')
    {
        $array = [];
        foreach ($this->clients as $client) {
            if ($client->status)
                $array[$client->id] = $client->name;
        }
        asort($array);

        return ($prompt) ? $array = array('' => 'Select client') + $array : $array;
    }

    /**
     * A Company has many trades (trades they are skilled in).
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function tradesSkilledIn()
    {
        return $this->belongsToMany('App\Models\Site\Planner\Trade', 'company_trade', 'company_id', 'trade_id');
    }

    /**
     * A list of trades that company is skilled in separated by ,
     *
     * @return string
     */
    public function tradesSkilledInSBC()
    {
        $string = '';
        foreach ($this->tradesSkilledIn as $trade) {
            if ($trade->status)
                $string .= $trade->name . ', ';
        }

        return rtrim($string, ', ');
    }

    /**
     * A company has many tasks (list of tasks they use for planner).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasksSkilledInSelect($prompt = '')
    {
        $array = [];

        $trade_count = count($this->tradesSkilledIn);
        foreach ($this->tradesSkilledIn as $trade) {
            $tasks = Task::where('trade_id', '=', $trade->id)->orderBy('name')->get();
            foreach ($tasks as $task) {
                if ($task->status)
                    $array[$task->id] = ($trade_count > 1) ? $trade->name . ':' . $task->name : $task->name;
            }
        }

        return ($prompt && count($array) > 1) ? $array = array('' => 'Select Task') + $array : $array;
    }

    /**
     * A Collection of standard trades (list of trades they use for company creation).
     *
     * @return Collection
     */
    public function tradeList()
    {
        return Trade::where('company_id', 1)->get();
    }

    /**
     * A Parent company has many trades (list of trades they use for planner).
     *
     * @return Collection
     */
    public function tradeListPlanner()
    {
        return Trade::where('company_id', 1)->orWhere('company_id', $this->id)->get();
    }

    /**
     * A array of standard trades (list of trades they use for company creation).
     *
     * @return array
     */
    public function tradeListSelect($prompt = '')
    {
        $array = [];
        foreach ($this->tradeList() as $trade) {
            if ($trade->status)
                $array[$trade->id] = $trade->name;
        }
        asort($array);

        return ($prompt && count($array) > 1) ? $array = array('' => 'Select Trade(s)') + $array : $array;
    }

    /**
     * A Parent company has many trades (list of trades they use for planner).
     *
     * @return array
     */
    public function tradeListPlannerSelect($prompt = '')
    {
        $array = [];
        foreach ($this->tradeListplanner() as $trade) {
            if ($trade->status)
                $array[$trade->id] = $trade->name;
        }
        asort($array);

        return ($prompt && count($array) > 1) ? $array = array('' => 'Select Trade(s)') + $array : $array;
    }


    /**
     * A Company has many child companies
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function childCompany()
    {
        return $this->hasMany('App\Models\Company\Company', 'parent_company');
    }

    /**
     * A Company has a Parent company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function parentCompany()
    {
        return $this->belongsTo('App\Models\Company\Company', 'parent_company');
    }

    /**
     * A Company reports to themselves OR a Parent company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function reportsTo()
    {
        return ($this->parentCompany) ? $this->parentCompany : $this;
    }

    /**
     * A list of companies this company has authority over
     * ie company has authority over itself + child companies
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function companies($status = '')
    {
        if ($this->id == 2) // Safworksite Website
            return ($status != '') ? Company::where('status', $status)->get() : Company::all();

        if ($status != '')
            return Company::where('status', $status)->where('parent_company', $this->id)->orWhere('id', $this->id)->get();

        return Company::where('parent_company', $this->id)->orWhere('id', $this->id)->get();;
    }

    /**
     * A dropdown list of companies who are child of this company ie.
     *
     * @return array
     */
    public function companiesSelect($prompt = '')
    {
        $array = [];
        foreach ($this->companies() as $company) {
            //$record = Company::find($company->id);
            if ($company->status)
                $array[$company->id] = $company->name_alias;
        }
        asort($array);

        if ($prompt == 'all')
            return ($prompt && count($array) > 1) ? $array = array('' => 'All Companies') + $array : $array;
        if ($prompt == 'ALL')
            return ($prompt && count($array) > 1) ? $array = array('all' => 'All Companies') + $array : $array;

        return ($prompt && count($array) > 1) ? $array = array('' => 'Select Company') + $array : $array;
    }

    /**
     * A list of users this company has authority over
     * ie company has authority own staff + child companies
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function users($status = '')
    {
        $company_list = $this->companies()->pluck('id')->toArray();

        return ($status != '') ? User::where('status', $status)->whereIn('company_id', $company_list)->get() : User::whereIn('company_id', $company_list)->get();
    }

    /**
     * A dropdown list of users who are this company has authority over ie.
     *
     * @return array
     */
    public function usersSelect($prompt = '')
    {
        $array = [];
        foreach ($this->users() as $user) {
            if ($user->status)
                $array[$user->id] = (Auth::user()->company->subscription > 1) ? $user->full_name . " (" . $user->company->name_alias . ")" : $user->full_name;
        }
        asort($array);

        if ($prompt == 'all')
            return ($prompt && count($array) > 1) ? $array = array('' => 'All Users') + $array : $array;
        if ($prompt == 'ALL')
            return ($prompt && count($array) > 1) ? $array = array('all' => 'All Users') + $array : $array;

        return ($prompt && count($array) > 1) ? $array = array('' => 'Select User') + $array : $array;
    }

    /**
     * A dropdown list of staff for this company.
     *
     * @return array
     */
    public function staffSelect($prompt = '')
    {
        $array = [];
        foreach ($this->staff as $user) {
            if ($user->status)
                $array[$user->id] = $user->full_name;
        }
        asort($array);

        if ($prompt == 'all')
            return ($prompt && count($array) > 1) ? $array = array('' => 'All Users') + $array : $array;
        if ($prompt == 'ALL')
            return ($prompt && count($array) > 1) ? $array = array('all' => 'All Users') + $array : $array;

        return ($prompt && count($array) > 1) ? $array = array('' => 'Select User') + $array : $array;
    }


    /**
     * A collection of staff with 'status'
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function staffStatus($status = '')
    {
        return ($status) ? User::where('status', $status)->where('company_id', $this->id)->orderBy('firstname')->get() : User::where('company_id', $this->id)->orderBy('firstname')->get();
    }

    /**
     * Deactivate all active staff
     */
    public function deactivateAllStaff()
    {
        $count = 0;
        foreach ($this->staffStatus(1) as $user) {
            if ($user->email) {
                $user->email = 'archived-' . $user->id . '-' . $user->email;
                if ($user->notes)
                    $user->notes .= "\nupdated email to " . $user->email . ' due to archiving';
                else
                    $user->notes = "updated email to " . $user->email . ' due to archiving';
            }
            $user->status = 0;
            $user->save();
            $count ++;
        }
        if ($count)
            Toastr::error("($count) Users deactivated");
    }

    /**
     *  Delete company from Planner from specified date and replace with generic trade (if possible).
     */
    public function onPlannerForTrade($trade_id, $past = false)
    {
        $planner = SitePlanner::where('entity_type', 'c')->where('entity_id', $this->id)
            ->whereIn('task_id', Trade::find($trade_id)->tasks->pluck('id')->toArray())
            ->where('to', '>', Carbon::today()->format('Y-m-d'))->get();
        foreach ($planner as $plan) {
            echo "plan:" . $plan->date . " task:" . $plan->task_id . "<br>";
            if (Task::find($plan->task_id)->trade->id == $old_trade->id) {
                $planned_trades[] = $old_trade->id;
            }

        }
    }

    /**
     *  Delete company from Planner from specified date and replace with generic trade (if possible).
     */
    public function deleteFromPlanner($date)
    {
        $planner = SitePlanner::where('entity_type', 'c')->where('entity_id', $this->id)
            ->where('to', '>', $date->format('Y-m-d'))->get();

        $count = 0;
        foreach ($planner as $plan) {
            $count ++;
            $trade_id = Task::findOrFail($plan->task_id)->trade_id;
            if ($trade_id) {
                $plan->entity_type = 't';
                $plan->entity_id = $trade_id;
                $plan->save();
            } else
                $plan->delete();

        }
        if ($count)
            Toastr::error("($count) Events deleted from planner ");
    }

    /**
     * A Company has many Users that Supervise it from Parent company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function supervisedBy()
    {
        return $this->belongsToMany('App\User', 'company_supervisors_transient', 'company_id', 'user_id');
    }

    /**
     * A list of users that supervise the company separated by ,
     *
     * @return string
     */
    public function supervisedBySBC()
    {
        $string = '';
        foreach ($this->supervisedBy as $user) {
            if ($user->status)
                $string .= $user->fullname . ', ';
        }

        return rtrim($string, ', ');
    }

    /**
     * A list of users this company has with 'security access'
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function securityUsers($status = '')
    {
        $staff = ($status == '') ? $this->staffStatus(1) : User::where('status', $status)->where('company_id', $this->id)->get();

        $array = [];
        foreach ($staff as $user) {
            if ($user->security)
                $array[] = $user->id;
        }

        return User::whereIn('id', $array)->get();
    }

    /**
     * A list of users that has 'security access' within the company separated by ,
     *
     * @return string
     */
    public function securityUsersSBC($status = '')
    {
        $string = '';
        foreach ($this->securityUsers($status) as $user)
            $string .= $user->fullname . ', ';

        return rtrim($string, ', ');
    }

    /**
     * A list of users this company with primary/secondary contact or is Admin
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function seniorUsers()
    {
        $array = [];
        // Include Primary + Secondary
        if ($this->primary_user)
            $array[] = $this->primary_user;
        if ($this->secondary_user)
            $array[] = $this->secondary_user;

        // Include any with Security
        foreach ($this->staffStatus(1) as $user) {
            if ($user->security)
                $array[] = $user->id;
        }

        return User::whereIn('id', $array)->get();
    }

    /**
     * A list of Seniors users separated by ,
     *
     * @return string
     */
    public function seniorUsersSBC()
    {
        $string = '';

        // Include Primary + Secondary
        if ($this->primary_user) {
            $string .= $this->primary_contact()->fullname . " <span class='badge badge-info badge-roundless'>P</span>";
            if ($this->primary_contact()->security)
                $string .= " <span class='badge badge-warning badge-roundless'>Sec</span>";
            $string .= ', ';
        }
        if ($this->secondary_user) {
            $string .= $this->secondary_contact()->fullname . " <span class='badge badge-info badge-roundless'>S</span>";
            if ($this->secondary_contact()->security)
                $string .= " <span class='badge badge-warning badge-roundless'>Sec</span>";
            $string .= ', ';
        }

        foreach ($this->seniorUsers() as $user) {
            if ($user->id != $this->primary_user && $user->id != $this->secondary_user)
                $string .= $user->fullname . " <span class='badge badge-warning badge-roundless'>Sec</span>,";
        }

        return rtrim($string, ', ');
    }

    /**
     * A list of senior users emails
     *
     * @return string
     */
    function seniorUsersEmail()
    {
        $email_array = [];
        foreach ($this->seniorUsers() as $user) {
            if (validEmail($user->email))
                $email_array[] = $user->email;
        }

        return $email_array;
    }

    /**
     * A dropdown list of roles for this company.
     *
     * @return array
     */
    public function rolesSelect($type = 'all')
    {
        switch ($type) {
            case 'ext' :
                $array = Role2::where('company_id', $this->id)->where('external', 1)->orderBy('name')->pluck('name', 'id')->toArray();
                break;
            case 'int' :
                $array = Role2::where('company_id', $this->id)->where('external', 0)->orderBy('name')->pluck('name', 'id')->toArray();
                break;
            case 'all' :
                $array = Role2::where('company_id', $this->id)->orderBy('name')->pluck('name', 'id')->toArray();
        }

        //return ($prompt && count($array) > 1) ? $array = array('' => 'Select Role') + $array : $array;
        return $array;
    }

    /**
     * A list of users this company has with 'certain' role
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function usersWithRole($role, $status = '')
    {
        $staff = ($status == '') ? $this->staffStatus(1) : User::where('status', $status)->where('company_id', $this->id)->get();

        $array = [];
        foreach ($staff as $user) {
            if ($user->hasRole2($role))
                $array[] = $user->id;
        }

        return User::whereIn('id', $array)->get();
    }

    /**
     * A list of users that has role 'certain' within the company separated by ,
     *
     * @return string
     */
    public function usersWithRoleSBC($role, $status = '')
    {
        $string = '';
        $staff = ($status == '') ? $this->staffStatus(1) : User::where('status', $status)->where('company_id', $this->id)->get();
        foreach ($staff as $user) {
            if ($user->hasRole2($role)) {
                $string .= $user->fullname;
                if ($user->security)
                    $string .= " <span class='badge badge-warning badge-roundless'>Sec</span>";
                if ($user->id == $this->primary_user)
                    $string .= " <span class='badge badge-info badge-roundless'>P</span> ";
                if ($user->id == $this->secondary_user)
                    $string .= " <span class='badge badge-info badge-roundless'>S</span> ";
                $string .= ', ';
            }
        }

        return rtrim($string, ', ');
    }

    /**
     * A list of users emails that has role 'certain' within the company
     *
     * @return string
     */
    function usersWithRoleEmail($role, $status = '')
    {
        $email_array = [];
        $staff = ($status == '') ? $this->staffStatus(1) : User::where('status', $status)->where('company_id', $this->id)->get();
        foreach ($staff as $user) {
            if ($user->hasRole2($role)) {
                if (validEmail($user->email))
                    $email_array[] = $user->email;
            }
        }

        return $email_array;
    }

    /**
     * A Unique list of supervisors this company has
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function supervisors()
    {
        $super_ids = DB::table('company_supervisors')->where('company_id', $this->id)->pluck('user_id')->toArray();

        return (User::find($super_ids));

        return DB::table('company_supervisors AS s')->select('s.id', 's.user_id',
            DB::raw('CONCAT(users.firstname, " ", users.lastname) AS fullname'))->where('s.company_id', $this->id)
            ->join('users', 's.user_id', '=', 'users.id')->groupBy('s.user_id')->get();
    }

    /**
     * A dropdown list of users that have the role 'supervisor' for company.
     *
     * @return array
     */
    public function supervisorsSelect($prompt = '')
    {
        $array = [];
        foreach ($this->supervisors() as $user)
            $array[$user->id] = $user->fullname;

        asort($array);

        return ($prompt) ? $array = array('' => 'Select supervisor') + $array : $array;
    }

    /**
     * A list of sites this company has authority over
     * ie company has authority all its clients sites
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function sites($status = '')
    {
        $client_list = $this->clients->pluck('id')->toArray();

        return ($status == '') ? Site::whereIn('client_id', $client_list)->get() : Site::where('status', $status)->whereIn('client_id', $client_list)->get();
    }

    /**
     * A dropdown list of sites this company has authority over
     *
     * @return array
     */
    public function sitesSelect($prompt = '', $status = '')
    {
        $array = [];
        foreach ($this->sites($status) as $site) {
            $record = Site::findOrFail($site->id);
            if ($record->status)
                $array[$site->id] = $record->name;
        }
        asort($array);

        if ($prompt == 'all')
            return ($prompt && count($array) > 1) ? $array = array('' => 'All sites') + $array : $array;
        if ($prompt == 'ALL')
            return ($prompt && count($array) > 1) ? $array = array('all' => 'All sites') + $array : $array;

        return ($prompt && count($array) > 1) ? $array = array('' => 'Select Site') + $array : $array;
    }

    /**
     * A list of sites this company is on a Planner for
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function sitesPlannedFor($status = '', $date_from = '', $date_to = '')
    {
        $client_list = $this->reportsTo()->clients->pluck('id')->toArray();
        $collection = ($status != '') ? Site::where('status', $status)->whereIn('client_id', $client_list)->get() : Site::whereIn('client_id', $client_list)->get();

        // If Company has no Parent then return full collection
        // ie. a list of all sites of all their own clients
        if (!$this->parentCompany)
            return $collection;

        $logged_site_id = '';
        if (Session::has('siteID')) {
            $site_code = Session::get('siteID');
            $site = Site::where(['code' => $site_code])->first();
            $logged_site_id = $site->id;
        }

        // Otherwise return a filtered collection of sites that company is on b ber for
        $filteredCollection = [];
        foreach ($collection as $site) {
            if ($date_from && $date_to)
                $onPlanner = SitePlanner::where('site_id', $site->id)
                    ->where('entity_type', 'c')->where('entity_id', $this->id)
                    ->whereDate('from', '>=', $date_from)->whereDate('to', '<=', $date_to)->first();
            else
                $onPlanner = SitePlanner::where('site_id', $site->id)
                    ->where('entity_type', 'c')->where('entity_id', $this->id)->first();
            if (!$onPlanner && $site->id != $logged_site_id)
                $filteredCollection[] = $site->id;
        }

        return $collection->except($filteredCollection);
    }

    /**
     * A dropdown list of sites this company is on a Planner for
     *
     * @return array
     */
    public function sitesPlannedForSelect($prompt = '', $status = '1')
    {
        $array = [];
        foreach ($this->sitesPlannedFor($status) as $site) {
            $record = Site::findOrFail($site->id);
            if ($record->status)
                $array[$site->id] = $record->name;
        }
        asort($array);

        return ($prompt && count($array) > 1) ? $array = array('' => 'Select Site') + $array : $array;
    }

    /**
     * A dropdown list of users that have the role 'supervisor' for company.
     *
     * @return array
     */
    public function workersCompCategorySelect($prompt = '')
    {
        if ($this->business_entity == 'Company' || $this->business_entity == 'Trading Trust')
            $array = [
                'a. Is a Propriety Limited Company (Pty Ltd)'                  => 'a. Is a Propriety Limited Company (Pty Ltd)',
                'b. Had a Workers Compensation Policy'                         => 'b. Had a Workers Compensation Policy',
                'c. Work by Quotes or Tender (must refer to quote on invoice)' => 'c. Work by Quotes or Tender (must refer to quote on invoice)'];
        else
            $array = [
                'c. Work by Quotes or Tender (must refer to quote on invoice)' => 'c. Work by Quotes or Tender (must refer to quote on invoice)',
                '3. Contract of service (Written or Implied) - Hourly'         => '3. Contract of service (Written or Implied) - Hourly'];

        return ($prompt) ? $array = array('' => 'Select category') + $array : $array;
    }

    /**
     * A Company may have many CompanyDocs
     *
     * @return Collection
     */
    public function companyDocs($category_id = '', $status = '')
    {
        if ($category_id)
            return ($status == '') ? CompanyDoc::where('category_id', $category_id)->where('for_company_id', $this->id)->get() :
                CompanyDoc::where('status', $status)->where('category_id', $category_id)->where('for_company_id', $this->id)->get();
        else
            return ($status == '') ? CompanyDoc::where('for_company_id', $this->id)->get() : CompanyDoc::where('status', $status)->where('for_company_id', $this->id)->get();
    }

    /**
     * Current CompanyDocs ie status > 0
     *
     * @return collection
     */
    /*
    public function currentCompanyDocs($status = '')
    {
        return CompanyDoc::where('for_company_id', $this->id)->where('status', '>', '0')->get();
    }*/

    /**
     * First active CompanyDoc of a specific type
     *
     * @return CompanyDoc record
     */
    public function activeCompanyDoc($category_id)
    {
        return CompanyDoc::where('category_id', $category_id)->where('for_company_id', $this->id)->where('status', '>', '0')->first();
    }

    /**
     * Expired CompanyDoc of a specific type
     *
     * @return CompanyDoc record
     */
    public function expiredCompanyDoc($category_id)
    {
        $doc = CompanyDoc::where('category_id', $category_id)->where('for_company_id', $this->id)->where('status', '>', '0')->first();
        if ($doc)
            return ($doc->expiry->lt(Carbon::today())) ? 'Expired ' . $doc->expiry->format('d/m/Y') : null;
        else
            return 'N/A';
    }

    /**
     * Dropdown Option for Contractor Licence
     *
     * @return string
     */
    public function contractorLicenceOptions()
    {
        $doc = CompanyDoc::where('category_id', 7)->where('for_company_id', $this->id)->where('status', '>', '0')->first();
        if ($doc)
            return ContractorLicence::find(1)->classOptions(explode(',', $doc->ref_type));

        return ContractorLicence::find(1)->classOptions();
    }

    /**
     * Contractor Licence Class SBC
     *
     * @return string
     */
    public function contractorLicenceSBC()
    {
        $str = '';
        $doc = CompanyDoc::where('category_id', 7)->where('for_company_id', $this->id)->where('status', '>', '0')->first();
        if ($doc) {
            foreach (explode(',', $doc->ref_type) as $class_id) {
                $lic = ContractorLicence::find($class_id);
                if ($lic)
                    $str .= $lic->name . ', ';
            }
        }

        return rtrim($str, ', ');
    }

    /**
     * Determine if a certain document type is Required
     *
     * @return boolean
     */
    public function requiresCompanyDoc($type)
    {
        // Doc types
        // 1 PL
        // 2 WC
        // 3 SA
        // 4 Sub
        // 5 PTC
        // 7 CL

        // Categories
        // '1' => 'Subcontractor (On Site Trade)',
        // '2' => 'Service Provider (On Site trade',
        // '3' => 'Service Provider (Off Site)',
        // '4' => 'Supply & Fit',
        // '5' => 'Supply Only',
        // '6' => 'Consultant',


        // Determine WC or SA
        if (in_array($this->category, [1, 2, 3, 4, 6])) {  // All but 'Supply Only'
            if ($type == 2 && in_array($this->business_entity, ['1', 'Company', '4', 'Trading Trust'])) return true;
            if ($type == 3 && in_array($this->business_entity, ['2', 'Partnership', '3', 'Sole Trader'])) return true;
        }

        // Subcontractor (On Site Trade)
        if ($this->category == 1 && in_array($type, [1, 4, 5])) return true; // Requires PL, Sub, PTC
        if ($this->category == 1 && $type == 7 && $this->tradeRequiresContractorsLicence()) return true; // Requires CL

        // Service Provider (On Site Trades) or Supply & Fit
        if (in_array($this->category, [2, 4]) && $type == 1) return true; // Requires PL
        if (in_array($this->category, [2, 4]) && $type == 7 && $this->tradeRequiresContractorsLicence()) return true; // Requires CL

        // Supply Only
        if ($this->category == 5 && $type == 1) return true; // Requires PL

        return false;
    }

    /**
     * Determine if a certain document type is Required
     *
     * @return boolean
     */
    public function requiresCompanyDocText($type)
    {
        return ($this->requiresCompanyDoc($type) ? '<span class="font-red">Required</span>' : '');
    }

    /**
     * Determine if Contractors Licence is Required for any of their trades
     *
     * @return boolean
     */
    public function requiresContractorsLicence()
    {
        return $this->requiresCompanyDoc(7);
    }

    /**
     * Determine if Contractors Licence is Required for any of their trades
     *
     * @return boolean
     */
    public function tradeRequiresContractorsLicence()
    {
        if ($this->category == '1' || $this->category == '2' || $this->category == '4') {
            foreach ($this->tradesSkilledIn as $trade) {
                if ($trade->licence_req)
                    return 1;
            }
        }

        return 0;
    }

    /**
     * Determine if Company is compliant ie. has required docs.
     *
     * @return booleen
     */
    public function compliantDocs()
    {
        $doc_types = [1, 2, 3, 4, 5];
        foreach ($doc_types as $type) {
            if ($this->requiresCompanyDoc($type) && (!$this->activeCompanyDoc($type) || $this->activeCompanyDoc($type)->status != 1))
                return false;
        }

        if ($this->licence_required && (!$this->activeCompanyDoc(7) || $this->activeCompanyDoc(7)->status != 1))
            return false;

        return true;
    }

    /**
     * Missing Company Info
     *
     * @return Text or Array
     */
    public function missingDocs($format = 'html')
    {
        $doc_types = [1 => 'Public Liability', 2 => "Worker's Compensation", 3 => 'Sickness & Accident Insurance', 4 => 'Subcontactors Statement', 5 => 'Period Trade Contract'];
        $missing_docs = [];
        $missing_html = '';

        foreach ($doc_types as $type => $name) {
            if ($this->requiresCompanyDoc($type) && (!$this->activeCompanyDoc($type) || $this->activeCompanyDoc($type)->status != 1)) {
                $missing_docs[$type] = $name;
                $missing_html .= "$name, ";
            }

        }

        if ($this->licence_required && (!$this->activeCompanyDoc(7) || $this->activeCompanyDoc(7)->status != 1)) {
            $missing_docs[7] = 'Contractor Licence';
            $missing_html .= 'Contractor Licence, ';
        }

        $missing_html = rtrim($missing_html, ', ');

        return ($format == 'html') ? $missing_html : $missing_docs;

    }

    /**
     * Missing Company Info
     *
     * @return string
     */
    public function missingInfo()
    {
        $today = Carbon::today();
        $str = '';
        $contact = '<b>Company Details:</b> ';
        if (!$this->email) $contact .= '<span class="font-red">email</span>, ';
        if (!$this->phone) $contact .= 'phone, ';
        if (!$this->address) $contact .= 'address, ';
        if (!$this->suburb) $contact .= 'suburb, ';
        if (!$this->state) $contact .= 'state, ';
        if (!$this->postcode) $contact .= 'postcode, ';
        if (!$this->primary_user) $contact .= '<span class="font-red">primary contact</span>, ';
        if ($contact != '<b>Company Details:</b> ')
            $str .= rtrim($contact, ', ') . '<br>';

        $details = '<b>Business Details:</b> ';
        if (!$this->abn) $details .= 'ABN, ';
        if (!$this->business_entity) $details .= 'Business Entity, ';
        if (!$this->gst) $details .= 'GST, ';
        if (!$this->creditor_code) $details .= 'Creditor Code, ';
        if ($details != '<b>Business Details:</b> ')
            $str .= rtrim($details, ', ') . '<br>';


        if ($this->missingDocs())
            $str .= "<b>Documents:</b> " . $this->missingDocs();

        return ($str) ? $str : null;

    }


    /**
     * A Company has many WMS Documents
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wmsdocs()
    {
        return $this->hasMany('App\Models\Safety\WmsDoc', 'for_company_id');
    }

    /**
     * A dropdown list of WMS templates this company has available
     *
     * @return array
     */
    public function wmsTemplateSelect($prompt = '')
    {
        $array = [];
        $templates = WmsDoc::where('master', '1')
            ->where(function ($q) {
                $q->where('company_id', Auth::user()->company_id);
                $q->orWhere('company_id', Auth::user()->company->reportsTo()->id);
            })
            ->where('status', '1')->get();

        foreach ($templates as $template) {
            $array[$template->id] = $template->name . ' v' . $template->version;
        }
        asort($array);

        return ($prompt && count($array) > 1) ? $array = array('' => 'Start from Scratch') + $array : $array;
    }

    /**
     * A dropdown list of WMS docs this company has available
     *
     * @return array
     */
    public function wmsDocSelect($prompt = '')
    {
        $array = [];
        $templates = WmsDoc::where('master', '0')
            ->where(function ($q) {
                $q->where('company_id', Auth::user()->company_id);
                $q->orWhere('for_company_id', Auth::user()->company_id);
            })
            ->where('status', '<>', 0)->get();

        foreach ($templates as $template) {
            $array[$template->id] = $template->name . ' v' . $template->version . ' (' . $template->principle . ')';
        }
        asort($array);

        return ($prompt && count($array) > 1) ? $array = array('' => 'Start from Scratch') + $array : $array;
    }

    /**
     * A Company has many ToolboxTalks
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function toolboxs()
    {
        //return $this->hasMany('App\Models\Safety\ToolboxTalk', 'for_company_id');
        return ToolboxTalk::where('company_id', '3')->orWhere('for_company_id', '3')->get();
    }

    /**
     * A dropdown list of Toolbox Talks templates this company has available
     *
     * @return array
     */
    public function toolboxTemplateSelect($prompt = '')
    {
        $array = [];
        $templates = ToolboxTalk::where('master', '1')
            ->where(function ($q) {
                $q->where('company_id', Auth::user()->company_id);
                $q->orWhere('company_id', Auth::user()->company->reportsTo()->id);
            })
            ->where('status', '1')->get();

        foreach ($templates as $template) {
            $array[$template->id] = $template->name . ' (v' . $template->version . ')';
        }
        asort($array);

        return ($prompt && count($array) > 1) ? $array = array('' => 'Start from Scratch') + $array : $array;
    }

    /**
     * A dropdown list of Toolbox Talks this company has available
     *
     * @return array
     */
    public function toolboxSelect($prompt = '')
    {
        $array = [];
        $templates = ToolboxTalk::where('master', '0')
            ->where(function ($q) {
                $q->where('company_id', Auth::user()->company_id);
                $q->orWhere('for_company_id', Auth::user()->company_id);
            })
            ->where('status', '<>', 0)->get();

        foreach ($templates as $template) {
            $array[$template->id] = $template->name . ' (v' . $template->version . ')';
        }
        asort($array);

        return ($prompt && count($array) > 1) ? $array = array('' => 'Start from Scratch') + $array : $array;
    }

    /**
     * A dropdown list of Tasks this company has available
     *
     * @return array
     */
    public function taskSelect($prompt = '')
    {
        $array = [];
        $trades = Trade::where('company_id', Auth::user()->company_id)->where('status', '1')->orderBy('name')->get();

        foreach ($trades as $trade) {
            $tasks = Task::where('trade_id', $trade->id)->where('status', '1')->orderBy('name')->get();
            foreach ($tasks as $task)
                $array[$task->id] = $trade->name . ' - ' . $task->name . ' (' . $task->code . ')';
        }

        //asort($array);

        return ($prompt && count($array) > 1) ? $array = array('' => 'Select task') + $array : $array;
    }

    /**
     * A dropdown list of sites with QA reports this company has authority over
     *
     * @return array
     */
    public function sitesQaSelect($status = '', $prompt = '')
    {
        $array = [];
        foreach ($this->sites($status) as $site) {
            //$record = Site::findOrFail($site->id);

            if ($site->qaReports->count()) {
                if ($status) {
                    if ($site->status == $status)
                        $array[$site->id] = $site->name;
                } else
                    $array[$site->id] = $site->name;
            }
        }
        asort($array);

        if ($prompt == 'all')
            return ($prompt && count($array) > 1) ? $array = array('' => 'All sites') + $array : $array;
        if ($prompt == 'ALL')
            return ($prompt && count($array) > 1) ? $array = array('all' => 'All sites') + $array : $array;

        return ($prompt && count($array) > 1) ? $array = array('' => 'Select Site') + $array : $array;
    }



    /**
     * A list is users from company 'company' with role(s) 'role1|role2'
     *
     * @return collection
     */
    /*public function usersWithRoles($roles, $company = '')
    {
        if ($company == 'all')
            $user_list = $this->users();
        elseif ($company)
            $user_list = $company->staff;
        else
            $user_list = $this->staff;

        $user_can_list = [];
        foreach ($user_list as $user) {
            if ($user->hasRole2($roles))
                $user_can_list[] = $user->id;
        }

        return User::whereIn('id', $user_can_list)->get();
    }*/

    /**
     * A Company has many Safetytips.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function safetytips()
    {
        return $this->hasMany('App\Models\Comms\SafetyTip');
    }

    /**
     * Current Parent Company Safety Tip
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function currentSafetytip()
    {
        return SafetyTip::where('company_id', $this->id)->where('status', '1')->first();
    }

    /**
     * A Company has many Notifications
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function notifications()
    {
        return $this->hasMany('App\Models\Misc\SettingsNotification');
    }

    /**
     * A Users with Notification of Type 'type'
     *
     * @return Collection
     */
    public function notificationsUsersType($type)
    {
        if (true || \App::environment('prod')) {
            if (!is_int($type))
                $type = SettingsNotificationTypes::type($type);

            $users = $this->notifications->where('type', $type)->pluck('user_id')->toArray();

            return ($users) ? User::find($users) : null;
        }

        return User::find(1);

    }

    /**
     * A Users with Notification of Type 'type'
     *
     * @return Array
     */
    public function notificationsUsersTypeArray($type)
    {
        if (true || \App::environment('prod')) {
            if (!is_int($type))
                $type = SettingsNotificationTypes::type($type);

            $users = $this->notifications->where('type', $type)->pluck('user_id')->toArray();

            return ($users) ? User::find($users)->pluck('id')->toArray() : [];
        }

        return [1];
    }

    /**
     * User Emails with Notification of Type 'type'
     *
     * @return Array
     */
    public function notificationsUsersEmailType($type)
    {
        $email_array = [];
        foreach ($this->notificationsUsersType($type) as $user) {
            if (validEmail($user->email))
                $email_array[] = $user->email;
        }

        return $email_array;
    }

    /**
     * Checks if company has certain 'addon'
     *
     * @return boolean
     */
    public function addon($type)
    {
        if ($this->id == '3')
            return true;

        return false;
    }

    /**
     * Get the owner of record  (getter)
     *
     * @return string;
     */
    public function getOwnedByAttribute()
    {
        return ($this->parentCompany) ? $this->parentCompany : $this;
    }

    /**
     * Display records last update_by + date
     *
     * @return string
     */
    public function displayUpdatedBy()
    {
        $user = User::findOrFail($this->updated_by);

        return '<span style="font-weight: 400">Last modified: </span>' . $this->updated_at->diffForHumans() . ' &nbsp; ' .
        '<span style="font-weight: 400">By:</span> ' . $user->fullname;
    }

    /**
     * Set the name + create slug attributes  (mutator)
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords(strtolower($value));
        $this->attributes['name'] = preg_replace_callback('/([.!?])\s*(\w)/', function ($matches) {
            return strtoupper($matches[1] . $matches[2]);
        }, ucfirst($this->attributes['name']));
        $this->attributes['name'] = preg_replace('/Pty/', 'PTY', $this->attributes['name']);
        $this->attributes['name'] = preg_replace('/Ltd/', 'LTD', $this->attributes['name']);
        $this->attributes['slug'] = getUniqueSlug($this, $value);
    }

    /**
     * Set the phone number to AU format  (mutator)
     */
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = format_phone('au', $value);
    }

    /**
     * Set the address to set format (mutator)
     */
    public function setAddressAttribute($value)
    {
        $this->attributes['address'] = trim(ucwords(strtolower($value)));
        $this->attributes['address'] = preg_replace('/Po Box/', 'PO BOX', $this->attributes['address']);
    }

    /**
     * Set the suburb to uppercase format  (mutator)
     */
    public function setSuburbAttribute($value)
    {
        $this->attributes['suburb'] = trim(strtoupper($value));
    }

    /**
     * Set the state to uppercase format  (mutator)
     */
    public function setStateAttribute($value)
    {
        $this->attributes['state'] = trim(strtoupper($value));
    }

    /**
     * Set the postcode format  (mutator)
     */
    public function setPostcodeAttribute($value)
    {
        $this->attributes['postcode'] = trim($value);
    }

    /**
     * Set the suburb to uppercase format  (mutator)
     */
    public function setAbnAttribute($value)
    {
        $stripped = preg_replace("/[^0-9]/", "", $value);
        $this->attributes['abn'] = preg_replace("/([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{3})/", "$1 $2 $3 $4", $stripped);
    }

    /**
     * Get the Company Id  (getter)
     */
    public function getCidAttribute()
    {
        return $this->id;
    }

    /**
     * Get the Name Alias  (getter)
     */
    public function getNameAliasAttribute()
    {
        if ($this->nickname)
            return $this->nickname;

        return $this->name;
    }

    /**
     * Get the Name Both  (getter)
     */
    public function getNameBothAttribute()
    {

        if ($this->nickname)
            return $this->name . ' <span class="font-grey-cascade">(' . $this->nickname . ')</span>';

        return $this->name;
    }

    /**
     * Get the Status Text Both  (getter)
     */
    public function getStatusTextAttribute()
    {

        if ($this->status == 1)
            return '<span class="font-green">ACTIVE</span>';

        if ($this->status == 1)
            return '<span class="font-yellow">PENDING</span>';

        if ($this->status == 0)
            return '<span class="font-red">INACTIVE</span>';

    }

    /**
     * Get the Name Alias  (getter)
     */
    public function getSubscriptionNameAttribute()
    {
        switch ($this->subscription) {
            case '0':
                return 'None';
            case '1':
                return 'Starter';
            case '2':
                return 'Professional';
            case '3':
                return 'Platinum';
            case '4':
                return 'Cape Cod Custom';
        }

        return $this->name;
    }

    /**
     * Get the suburb, state, postcode  (getter)
     */
    public function getSuburbStatePostcodeAttribute()
    {
        $string = strtoupper($this->attributes['suburb']);
        if ($this->attributes['suburb'] && $this->attributes['state'])
            $string .= ', ';
        if ($this->attributes['state'])
            $string .= $this->attributes['state'];
        if ($this->attributes['postcode'])
            $string .= ' ' . $this->attributes['postcode'];

        return $string;
    }

    /**
     * Get the suburb, state, postcode  (getter)
     */
    public function getAddressFormattedAttribute()
    {
        $string = '';

        if ($this->attributes['address'])
            $string = strtoupper($this->attributes['address']) . '<br>';

        $string .= strtoupper($this->attributes['suburb']);
        if ($this->attributes['suburb'] && $this->attributes['state'])
            $string .= ', ';
        if ($this->attributes['state'])
            $string .= $this->attributes['state'];
        if ($this->attributes['postcode'])
            $string .= ' ' . $this->attributes['postcode'];

        return ($string) ? $string : '-';
    }

    /**
     * Get the upcoming leave for company  (getter)
     */
    public function getLeaveUpcomingDatesAttribute()
    {
        $string = '';
        $leave = CompanyLeave::where('to', '>=', Carbon::today()->toDateTimeString())->where('company_id', $this->id)->orderBy('from')->get();

        foreach ($leave as $l) {
            if ($l->from == $l->to)
                $string .= $l->from->format('d/m') . ', ';
            else
                $string .= $l->from->format('d/m') . ' - ' . $l->to->format('d/m') . ', ';
        }

        return rtrim($string, ', ');
    }

    /**
     * The "booting" method of the model.
     *
     * Overrides parent function
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        if (Auth::check()) {
            // create a event to happen on creating
            static::creating(function ($table) {
                $table->created_by = Auth::user()->id;
                $table->updated_by = Auth::user()->id;
            });

            // create a event to happen on updating
            static::updating(function ($table) {
                $table->updated_by = Auth::user()->id;
            });
        }
    }
}

