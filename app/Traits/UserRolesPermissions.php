<?php
namespace App\Traits;

use DB;
use App\User;
use App\Models\Company\Company;
use App\Models\Site\Site;
use App\Models\Site\Planner\SitePlanner;
use App\Models\Misc\Role2;
use App\Models\Misc\Permission2;


trait UserRolesPermissions {

    /**
     * Check if a user has a certain 'role'
     *
     * @return boolean
     */
    public function hasRole2($role)
    {
        return ($this->roles2()->where('slug', $role)->first()) ? true : false;
    }

    /**
     * Check if a user has any of the given 'roles'
     *
     * @return boolean
     */
    public function hasAnyRole2($roles)
    {
        $roles_array = explode('|', $roles);
        foreach ($roles_array as $role) {
            if ($this->hasRole2($role))
                return true;
        }

        return false;
    }

    /**
     * Attach role to a user for company 'company'
     *
     * @param  $permission
     * @return int|bool
     */
    public function attachRole2($role)
    {
        // Determine if exists
        $exists = DB::table('role_user')->where(['user_id' => $this->id, 'role_id' => $role])->first();

        return ($exists) ? true : DB::table('role_user')->insert(['user_id' => $this->id, 'role_id' => $role]);
    }

    /**
     * Detach role from a user for company 'company'
     *
     * @param $permission
     * @return int
     */
    public function detachRole2($role)
    {
        return DB::table('role_user')->where(['user_id' => $this->id, 'role_id' => $role])->delete();
    }

    /**
     * Detach all roles from a user for company 'company'
     *
     * @return int
     */
    public function detachAllRoles2($company_id)
    {
        $company_role_ids = Role2::where('company_id', $company_id)->pluck('id')->toArray();

        return DB::table('role_user')->where('user_id', $this->id)->whereIn('role_id', $company_role_ids)->delete();
    }

    /**
     * User roles separated by Comma
     * @return string
     */
    public function rolesSBC()
    {
        $role_ids = Role2::where('company_id', $this->company_id)->pluck('id')->toArray();
        $roles = DB::table('role_user')->where('user_id', $this->id)->whereIn('role_id', $role_ids)
            ->join('roles', 'role_user.role_id', '=', 'roles.id')->orderBy('roles.name')->get();

        $string = '';
        foreach ($roles as $role)
            $string .= $role->name . ', ';

        return rtrim($string, ', ');
    }

    /**
     * User roles separated by Comma
     * @return string
     */
    public function parentRolesSBC()
    {
        $role_ids = Role2::where('company_id', $this->company->reportsTo()->id)->pluck('id')->toArray();
        $roles = DB::table('role_user')->where('user_id', $this->id)->whereIn('role_id', $role_ids)
            ->join('roles', 'role_user.role_id', '=', 'roles.id')->orderBy('roles.name')->get();

        $string = '';
        foreach ($roles as $role)
            $string .= $role->name . ', ';

        return rtrim($string, ', ');
    }



    /*   --                 --   */
    /*   --   Permissions   --   */
    /*   --                 --   */


    /**
     * Attach permission to a user for company 'company'
     *
     * @param  $permission
     * @return int|bool
     */
    public function attachPermission2($permission, $level, $company_id)
    {
        // Determine if exists
        $exists = DB::table('permission_user')->where(['user_id' => $this->id, 'permission_id' => $permission, 'company_id' => $company_id])->first();

        return ($exists) ? true : DB::table('permission_user')->insert(['user_id' => $this->id, 'permission_id' => $permission, 'level' => $level, 'company_id' => $company_id]);
    }

    /**
     * Detach permission from a user for company 'company'
     *
     * @param $permission
     * @return int
     */
    public function detachPermission2($permission, $company_id)
    {
        return DB::table('permission_user')->where(['user_id' => $this->id, 'permission_id' => $permission, 'company_id' => $company_id])->delete();
    }

    /**
     * Detach all permissions from a user for 'company'
     *
     * @return int
     */
    public function detachAllPermissions2($company_id)
    {
        return DB::table('permission_user')->where(['user_id' => $this->id, 'company_id' => $company_id])->delete();
    }

    /**
     * Determine if user has any Permission of 'type'
     *
     * @return int
     */
    public function hasAnyPermissionType($type)
    {
        $permission_types = ['view', 'edit', 'add', 'del', 'sig'];
        $permission_array = explode('|', $type);
        foreach ($permission_array as $permission) {
            foreach ($permission_types as $ptype) {
                if ($this->permissionLevel("$ptype.$permission", $this->company_id) || $this->permissionLevel("$ptype.$permission", $this->company->reportsTo()->id))
                    return true;
            }
        }

        return false;
    }

    /**
     * Check if a user has a certain 'permission'
     *
     * @return boolean
     */
    public function hasPermission2($permission)
    {
        // Get permission level attached to user
        if ($this->permissionLevel($permission, $this->company_id)) return true;
        if ($this->permissionLevel($permission, $this->company->reportsTo()->id)) return true;

        return false;
    }

    /**
     * Check if a user has any of the given 'permission'
     *
     * @return boolean
     */
    public function hasAnyPermission2($permissions)
    {
        $permissions_array = explode('|', $permissions);
        foreach ($permissions_array as $permission) {
            if ($this->hasPermission2($permission))
                return true;
        }

        return false;
    }


    /**
     * Determine level of a permission for a 'company'
     *
     * @param  $permission , company_id
     * @return int
     */
    public function userPermissionLevel($permission, $company_id)
    {
        $permission_id = $permission;
        if (!is_int($permission)) {
            // Get Permission ID
            $perm = Permission2::where('slug', $permission)->first();
            if (!$perm)
                return 0;
            $permission_id = $perm->id;
        }

        // Get permission level attached to user
        $permssion_user = DB::table('permission_user')->where('permission_id', $permission_id)->where('user_id', $this->id)->where('company_id', $company_id)->first();
        $level = ($permssion_user) ? $permssion_user->level : 0;

        return $level;
    }

    /**
     * Determine level of a permission for a 'company'
     *
     * @param  $permission , company_id
     * @return int
     */
    public function rolesPermissionLevel($permission, $company_id)
    {
        $permission_id = $permission;
        if (!is_int($permission)) {
            // Get Permission ID
            $perm = Permission2::where('slug', $permission)->first();
            if (!$perm)
                return 0;
            $permission_id = $perm->id;
        }

        // Array of role ids the users has with given company 'company_id'
        $company_role_ids = Role2::where('company_id', $company_id)->pluck('id')->toArray();
        $user_role_ids = DB::table('role_user')->where('user_id', $this->id)->whereIn('role_id', $company_role_ids)->pluck('role_id')->toArray();

        // Get permission level attached to users role
        $level = 0;
        $permssion_role = DB::table('permission_role')->where('permission_id', $permission_id)->whereIn('role_id', $user_role_ids)->where('company_id', $company_id)->get();
        //dd($permssion_role);
        foreach ($permssion_role as $p) {
            if ($p->level > $level)
                $level = $p->level;
        }

        return $level;
    }

    /**
     * Determine level of a permission for 'company'
     *
     * @param  $permission , company_id
     * @return int
     */
    public function permissionLevel($permission, $company_id)
    {
        $user_level = $this->userPermissionLevel($permission, $company_id);
        $role_level = $this->rolesPermissionLevel($permission, $company_id);

        return ($user_level > $role_level) ? $user_level : $role_level;
    }

    /**
     * A list of users this user has authority over
     * ie user has authority own themselves + maybe own companies/child users (if appropriate permission granted)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function authUsers($permission, $status = '')
    {
        // Company
        $company_level = $this->permissionLevel($permission, $this->company_id);
        $company_ids = [];
        if ($company_level == '99') $company_ids = $this->company->users()->pluck('id')->toArray(); // All
        if ($company_level == '50') $company_ids = $this->company->staff->pluck('id')->toArray(); // Our Company
        if ($company_level == '20') $company_ids = $this->company->staff->pluck('id')->toArray(); // Own Company
        if ($company_level == '10') $company_ids = [$this->id]; // Individual Only
        if ($company_level == '1') $company_ids = $this->company->users()->pluck('id')->toArray(); // Delete / Sign Off All

        // Parent Company
        $parent_level = $this->permissionLevel($permission, $this->company->reportsTo()->id);
        $parent_ids = [];
        if ($parent_level == '99') $parent_ids = $this->company->reportsTo()->users()->pluck('id')->toArray(); // All
        if ($parent_level == '50') $parent_ids = $this->company->reportsTo()->staff->pluck('id')->toArray(); // Our Company
        if ($parent_level == '20') $parent_ids = $this->company->users()->pluck('id')->toArray(); // Own Company
        if ($parent_level == '10') $parent_ids = [$this->id]; // Individual Only
        if ($parent_level == '1') $parent_ids = $this->company->reportsTo()->users()->pluck('id')->toArray(); // Delete / Sign Off All

        $merged_ids = array_merge($company_ids, $parent_ids);

        return ($status != '') ? User::where('status', $status)->whereIn('id', $merged_ids)->get() : User::whereIn('id', $merged_ids)->get();
    }

    /**
     * A list of company this user has authority over
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function authCompanies($permission, $status = '')
    {
        // Company
        $company_level = $this->permissionLevel($permission, $this->company_id);
        $company_ids = [];
        if ($company_level == '99') $company_ids = $this->company->companies()->pluck('id')->toArray(); // All
        if ($company_level == '20') $company_ids = $this->company->companies()->pluck('id')->toArray(); // Own Company
        if ($company_level == '1') $company_ids = $this->company->companies()->pluck('id')->toArray(); // Delete / Sign Off All

        // Parent Company
        $parent_level = $this->permissionLevel($permission, $this->company->reportsTo()->id);
        $parent_ids = [];
        if ($parent_level == '99') $parent_ids = $this->company->reportsTo()->companies()->pluck('id')->toArray(); // All
        if ($parent_level == '20') $parent_ids = $this->company->companies()->pluck('id')->toArray(); // Own Company
        if ($parent_level == '1') $parent_ids = $this->company->reportsTo()->companies()->pluck('id')->toArray(); // Delete / Sign Off All

        $merged_ids = array_merge($company_ids, $parent_ids);

        return ($status != '') ? Company::where('status', $status)->whereIn('id', $merged_ids)->get() : Company::whereIn('id', $merged_ids)->get();
    }

    /**
     * A list of sites this user has authority over
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function authSites($permission, $status = '')
    {
        // Company
        $company_level = $this->permissionLevel($permission, $this->company_id);
        $company_ids = [];
        if ($company_level == '99') $company_ids = $this->company->sites()->pluck('id')->toArray(); // All
        if ($company_level == '50') $company_ids = $this->company->sites()->pluck('id')->toArray(); // Our Company
        if ($company_level == '40') $company_ids = $this->areaSites()->pluck('id')->toArray(); // Supervisor for
        if ($company_level == '30') $company_ids = $this->company->sitesPlannedFor()->pluck('id')->toArray(); // Planned for
        if ($company_level == '1') $company_ids = $this->company->sites()->pluck('id')->toArray(); // Delete / Sign Off All

        // Parent Company
        $parent_level = $this->permissionLevel($permission, $this->company->reportsTo()->id);
        $parent_ids = [];
        if ($parent_level == '99') $parent_ids = $this->company->reportsTo()->sites()->pluck('id')->toArray(); // All
        if ($parent_level == '50') $parent_ids = $this->company->reportsTo()->sites()->pluck('id')->toArray(); // Our Company
        if ($parent_level == '30') $parent_ids = $this->company->sitesPlannedFor()->pluck('id')->toArray(); // Planned for
        if ($parent_level == '20') $parent_ids = $this->company->sitesPlannedFor()->pluck('id')->toArray(); // Planned for
        if ($parent_level == '1') $parent_ids = $this->company->reportsTo()->sites()->pluck('id')->toArray(); // Delete / Sign Off All

        $merged_ids = array_merge($company_ids, $parent_ids);

        return ($status != '') ? Site::where('status', $status)->whereIn('id', $merged_ids)->get() : Site::whereIn('id', $merged_ids)->orderBy('name')->get();
        //return ($status != '') ? Site::where('status', $status)->whereIn('id', $merged_ids)->orderBy('name')->get() : Site::whereIn('id', $merged_ids)->orderBy('name')->get();
    }

    /**
     * A dropdown list of sites this user has authority over
     *
     * @parms Permission, Status (site), Prompt, Started (whether Site has tasks on it)
     * @return array
     */
    public function authSitesSelect($permission, $status = '', $prompt = '', $started = '')
    {
        $sites = $this->authSites($permission, $status);

        $array = [];
        foreach ($sites as $site) {
            // Determine if Job Started is required or not
            $start = ($started) ? SitePlanner::where('site_id', $site->id)->first() : true;
            if ($start)
                $array[$site->id] = $site->name;
        }
        asort($array);

        if ($prompt == 'all')
            return ($prompt && count($array) > 1) ? $array = array('' => 'All Sites') + $array : $array;

        return ($prompt && count($array) > 1) ? $array = array('' => 'Select Site') + $array : $array;
    }


    /**
     * Verify if the user is allowed to perform certain action on a specific record
     *
     * @return boolean
     */
    public function allowed2($permission, $record = '')
    {
        list($action, $permissiontype) = explode('.', $permission, 2);

        // User can always view/edit own profile
        if (($permission == 'view.user' || $permission == 'edit.user') && $record->id == $this->id)
            return true;

        // User can always add.todoo
        if ($permission == 'add.todo')
            return true;

        // Support Tickets
        if ($permission == 'view.support.ticket' || $permission == 'edit.support.ticket') {
            if ($record->created_by == $this->id) // User can always view/edit own record
                return true;
            if ($this->security && in_array($record->created_by, $this->company->users()->pluck('id')->toArray())) // User belongs to own or child company
                return true;

            return false;
        }

        // Company Docs - Show
        if ($permission == 'show.company.doc.gen' || $permission == 'show.company.doc.lic' || $permission == 'show.company.doc.ics' || $permission == 'show.company.doc.whs') {
            if ($this->permissionLevel("view.$permissiontype", $record->id) || $this->permissionLevel("view.$permissiontype", $record->reportsTo()->id))
                return true;

            return false;
        }

        // Get permission levels
        $company_level = $this->permissionLevel($permission, $this->company_id);
        $parent_level = $this->permissionLevel($permission, $this->company->reportsTo()->id);

        // Return false if Company + Parent levels == 0
        if ($company_level == 0 && $parent_level == 0)
            return false;

        if ($action == 'add') // Don't need any further checking because 'add' doesn't affect any specific record.
            return true;      //  - also we know they must have 'add' permission if they reached this far.
        else {
            // -user, -company, client,
            // -site, -site.accident, -site.hazard, -site.doc, -site.qa, -site.asbestos, -site.doc.manager, -site.export
            // -weekly.planner, -trade.planner, -site.planner, -area.super, trade,
            // -attendance, -compliance
            // -safety.doc, general.doc, -company.doc, doc.pp, doc.stand, -sds, -toolbox, -wms, manage.report, -safetytip, -notify, -todo,
            // role, settings, company.trades

            // trade +  area.super relates to none record
            //
            //  ['0' => 'No', '99' => "All", '50' => "Our Company", '40' => 'Supervisor for', '30' => 'Planned for', '20' => 'Own Company', '10' => "Individual Only"]

            //$record_has_company_id = ['user', 'client', 'site.doc', 'site.qa', 'site.asbestos'];
            //$record_has_for_company_id = ['site.doc'];
            //$record_has_site_id = ['site.doc', 'site.accident', 'site.hazard', 'site.qa', 'site.asbestos', 'attendance', 'compliance'];

            // Users
            if ($permissiontype == 'user') {
                if ($record->id == $this->id && ($action == 'view' || $action == 'edit')) return true;  // User can always view/edit own profile
                if ($this->authUsers($permission)->contains('id', $record->id)) return true;

                return false;
            }

            // Companies
            if ($permissiontype == 'company') {
                if ($action == 'del' && $record->id == $this->company_id) return false; // User can't delete own company
                if ($this->authCompanies($permission)->contains('id', $record->id)) return true;

                return false;
            }

            // Company Accounting
            if ($permissiontype == 'company.accounting') {
                //if ($this->hasPermission2($permission) && $record->id == $this->company_id) // User belong to same company record
                //    return true;
                if ($this->authCompanies($permission)->contains('id', $record->id) && $record->id != $this->company_id) return true;

                return false;
            }

            // Company Documents
            if ($permissiontype == 'company.doc.gen' || $permissiontype == 'company.doc.lic' || $permissiontype == 'company.doc.whs' || $permissiontype == 'company.doc.ics') {
                // User can View or Update document if status is 2 or 3 ie. Pending/Rejected
                if ($action == 'view' || $record->status == '2' || $record->status == '3') {
                    if ($this->permissionLevel($permission, $record->company_id) == 99 || $this->permissionLevel($permission, $record->company_id) == 1) return true;  // User has 'All' permission to this record
                    if ($this->permissionLevel($permission, $record->company_id) == 20 && $record->for_company_id == $this->company_id) return true; // User has 'Own Company' permission so record must be 'for' their company
                } elseif ($this->permissionLevel("sig.$permissiontype", $record->company_id) == 1) {
                    // User requires 'Sign Off' at Document Owner level to update an active document
                    if ($this->permissionLevel($permission, $record->company_id) == 99 || $this->permissionLevel($permission, $record->company_id) == 1) return true;  // User has 'All' permission to this record
                    if ($this->permissionLevel($permission, $record->company_id) == 20 && $record->for_company_id == $this->company_id) return true; // User has 'Own Company' permission so record must be 'for' their company
                }

                return false;
            }


            // Sites + Planners (Weekly/Site/Trade)
            if ($permissiontype == 'site' || $permissiontype == 'site.admin' || $permissiontype == 'weekly.planner' || $permissiontype == 'site.planner' || $permissiontype == 'trade.planner') {
                if ($this->authSites($permission)->contains('id', $record->id)) return true;

                return false;
            }

            // Site Accident + Hazard
            if ($permissiontype == 'site.accident' || $permissiontype == 'site.hazard') {
                if ($company_level == 30 || $company_level == 40 || $parent_level == 30 || $parent_level == 40) {
                    // Planned For '30' or Supervisor For '40' so check site
                    if ($this->authSites($permission)->contains('id', $record->site_id)) return true;
                }
                // check users
                if ($this->authUsers($permission)->contains('id', $record->created_by)) return true;

                return false;
            }


            // Site (Doc, QA, Asbestos, Export) + Attendance + Compliance + Safety Doc
            if ($permissiontype == 'site.doc' || $permissiontype == 'site.qa' || $permissiontype == 'site.asbestos' || $permissiontype == 'site.export' ||
                $permissiontype == 'attendance' || $permissiontype == 'compliance' || $permissiontype == 'safety.doc'
            ) {
                if ($this->authSites($permission)->contains('id', $record->site_id)) return true;

                return false;
            }

            // Toolbox + WMS
            if ($permissiontype == 'toolbox' || $permissiontype == 'wms') {
                if ($permissiontype == 'toolbox' && $action == 'view' && $record->isAssignedToUser($this)) return true; // Toolbox Assigned to user
                if ($action == 'view' && $record->master && $record->company_id == '3') return true; // User can view library
                if ($this->permissionLevel($permission, $record->company_id) == 99 || $this->permissionLevel($permission, $record->company_id) == 1) return true;  // User has 'All' permission to this record
                if ($this->permissionLevel($permission, $record->company_id) == 20 && $record->for_company_id == $this->company_id) return true; // User has 'Own Company' permission so record must be 'for' their company

                return false;
            }

            // Safetytip + Notify + SDS
            if ($permissiontype == 'safetytip' || $permission == 'notify' || $permission == 'sds') {
                if ($this->hasPermission2($permission))
                    return true;
            }

            // Settings
            if ($permissiontype == 'settings') {
                if ($this->hasPermission2($permission) && $record->company_id == $this->company_id) // User belong to same company record
                    return true;

                return false;
            }

            // Area Super - Needs to be fixed for Multiple level 2 companies
            if ($permission == 'area.super') {
                if ($this->hasAnyPermission2($permission) && ($record->company_id == $this->company_id)) // User belong to same company record
                    return true;
            }

        }
    }
}