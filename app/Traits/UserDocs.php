<?php
namespace App\Traits;

use DB;
use Auth;
use Session;
use App\User;
use App\Models\Company\Company;
use App\Models\Company\CompanyDoc;
use App\Models\Company\CompanyDocCategory;
use App\Models\Site\Site;
use App\Models\Site\Planner\SitePlanner;
use App\Models\Misc\Role2;
use App\Models\Misc\Permission2;
use App\Models\User\UserDoc;
use App\Models\User\UserDocCategory;
use App\Http\Utilities\UserDocTypes;
use App\Http\Utilities\CompanyDocTypes;
use Carbon\Carbon;


trait UserDocs {

    /**
     * Missing Company Documents to be compliant
     *
     * @return Text or Array
     */
    public function missingDocs($format = 'array')
    {
        /*
        $doc_types = [1 => 'Public Liability', 2 => "Worker's Compensation", 3 => 'Sickness & Accident Insurance', 4 => 'Subcontractors Statement', 5 => 'Period Trade Contract', 7 => 'Contractor Licence'];
        $missing_docs = [];
        $missing_html = '';

        foreach ($doc_types as $type => $name) {
            if ($this->requiresUserDoc($type) && (!$this->activeUserDoc($type) || $this->activeUserDoc($type)->status != 1)) {
                $missing_docs[$type] = $name;
                if ($this->activeUserDoc($type)) {
                    $missing_status = ($this->activeUserDoc($type)->status == 2) ? 'label-warning' : 'label-danger';
                    $missing_html .= "<span class='label label-sm $missing_status'>$name</span>, ";
                } else
                    $missing_html .= "$name, ";
            }

        }

        $missing_html = rtrim($missing_html, ', ');

        return ($format == 'csv') ? $missing_html : $missing_docs;

        */
    }

    /**
     * A User may have many UserDocs
     *
     * @return Collection
     */
    public function userDocs($category_id = '', $status = '')
    {
        if ($category_id)
            return ($status == '') ? UserDoc::where('category_id', $category_id)->where('for_company_id', $this->id)->get() :
                UserDoc::where('status', $status)->where('category_id', $category_id)->where('for_company_id', $this->id)->get();
        else
            return ($status == '') ? UserDoc::where('user_id', $this->id)->get() : UserDoc::where('status', $status)->where('user_id', $this->id)->get();
    }

    /**
     * First active UserDoc of a specific type
     *
     * @return UserDoc record
     */
    public function activeUserDoc($category_id)
    {
        return UserDoc::where('category_id', $category_id)->where('user_id', $this->id)->where('status', '>', '0')->first();
    }

    /**
     * Expired UserDoc of a specific type
     *
     * @return UserDoc record
     */
    public function expiredUserDoc($category_id)
    {
        $doc = UserDoc::where('category_id', $category_id)->where('user_id', $this->id)->where('status', '>', '0')->first();
        if ($doc)
            return ($doc->expiry->lt(Carbon::today())) ? 'Expired ' . $doc->expiry->format('d/m/Y') : null;
        else
            return 'N/A';
    }

    /**
     * A dropdown list of types of Company Document Types user can access
     *
     * @return array
     */
    public function userDocTypeSelect($action, $user, $prompt = '')
    {
        $array = [];
        $single = DB::table('user_docs_categories')->whereIn('company_id', ['1', '3'])->where('multiple', 0)->pluck('id')->toArray();
        //$single = DB::table('user_docs_categories')->whereIn('company_id', ['1', Auth::user()->company_id])->where('multiple', 0)->pluck('id')->toArray();
        foreach (UserDocTypes::all() as $doc_type => $doc_name) {
            // Public Docs
            if ($this->hasPermission2("$action.docs.$doc_type.pub") || $this->hasPermission2("$action.docs.$doc_type.pri")) {
                foreach (UserDocTypes::docs($doc_type, 0)->pluck('name', 'id')->toArray() as $id => $name) {
                    if (!($action == 'add' && in_array($id, $single) && $this->activeUserDoc($id)))
                        $array[$id] = $name;
                }
            }
            // Private Docs
            if ($this->hasPermission2("$action.docs.$doc_type.pri")) {
                foreach (UserDocTypes::docs($doc_type, 1)->pluck('name', 'id')->toArray() as $id => $name) {
                    if (!($action == 'add' && in_array($id, $single) && $this->activeUserDoc($id)))
                        $array[$id] = $name;
                }
            }
        }

        asort($array);

        if ($prompt == 'all')
            return ($prompt && count($array) > 1) ? $array = array('ALL' => 'All categories') + $array : $array;

        return ($prompt && count($array) > 1) ? $array = array('' => 'Select Type') + $array : $array;
    }

    /****************************
     *
     * Company Docs
     *
     ***************************/

    /**
     * A dropdown list of types of Company Document Departments user can access
     *
     * @return array
     */
    public function companyDocDeptSelect($action, $company, $prompt = '')
    {
        $array = [];
        foreach (CompanyDocTypes::all() as $doc_type => $doc_name) {
            // Public Docs
            if ($this->hasPermission2("$action.docs.$doc_type.pub"))
                $array[$doc_type] = $doc_name;
            // Private Docs
            if ($this->hasPermission2("$action.docs.$doc_type.pri"))
                $array[$doc_type] = $doc_name;
        }

        asort($array);

        if ($prompt == 'all')
            return ($prompt && count($array) > 1) ? $array = array('all' => 'All departments') + $array : $array;

        return ($prompt && count($array) > 1) ? $array = array('' => 'Select Type') + $array : $array;
    }

    /**
     * A dropdown list of types of Company Document Types user can access
     *
     * @return array
     */
    public function companyDocTypeSelect($action, $company, $prompt = '')
    {
        $array = [];
        $array_ss_ptc = [];
        $single = DB::table('company_docs_categories')->whereIn('company_id', ['1', Auth::user()->company_id])->where('multiple', 0)->pluck('id')->toArray();
        foreach (CompanyDocTypes::all() as $doc_type => $doc_name) {
            // Public Docs
            if ($this->hasPermission2("$action.docs.$doc_type.pub") || $this->hasPermission2("$action.docs.$doc_type.pri")) {
                foreach (CompanyDocTypes::docs($doc_type, 0)->pluck('name', 'id')->toArray() as $id => $name) {
                    if (!($action == 'add' && in_array($id, $single) && $company->activeCompanyDoc($id)))
                        $array[$id] = $name;
                    if (!($action == 'add' && in_array($id, $single) && $company->activeCompanyDoc($id)) && !in_array($id, [4,5]))
                        $array_ss_ptc[$id] = $name;
                }
            }
            // Private Docs
            if ($this->hasPermission2("$action.docs.$doc_type.pri")) {
                foreach (CompanyDocTypes::docs($doc_type, 1)->pluck('name', 'id')->toArray() as $id => $name) {
                    if (!($action == 'add' && in_array($id, $single) && $company->activeCompanyDoc($id)))
                        $array[$id] = $name;
                    if (!($action == 'add' && in_array($id, $single) && $company->activeCompanyDoc($id)) && !in_array($id, [4,5]))
                        $array_ss_ptc[$id] = $name;
                }
            }
        }

        asort($array);
        asort($array_ss_ptc);

        if ($prompt == 'all')
            return (count($array) > 1) ? $array = array('ALL' => 'All categories') + $array : $array;
        if ($prompt == '-SS-PTC')
            return (count($array_ss_ptc) > 1) ? $array_ss_ptc = array('' => 'Select Type') + $array_ss_ptc : $array_ss_ptc;

        return ($prompt && count($array) > 1) ? $array = array('' => 'Select Type') + $array : $array;
    }

}