<?php

namespace App\Http\Controllers\Misc;


use Illuminate\Http\Request;

use DB;
use App\Models\Misc\Role2;
use App\Models\Misc\Permission2;
use App\Http\Requests;
use App\Http\Requests\Misc\RoleRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use nilsenj\Toastr\Facades\Toastr;
use Carbon\Carbon;
use App\Models\Misc\PermissionRoleCompany;

class RoleController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Check authorisation and throw 404 if not
        if (!(Auth::user()->company->subscription && Auth::user()->hasAnyPermissionType('settings')))
            return view('errors/404');

        $roles = Role2::all()->sortBy('name');

        return view('manage/settings/role/list', compact('roles'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasPermission2('edit.settings'))
            return view('errors/404');

        return view('manage/settings/role/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasPermission2('edit.settings'))
            return view('errors/404');

        // Create Role
        $role_request = $request->all();
        $role_request['company_id'] = Auth::user()->company_id;
        //dd($role_request);
        Role2::create($role_request);
        Toastr::success("Created new role");

        return redirect('/settings/role');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role2::findorFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('view.settings', $role) || !Auth::user()->allowed2('edit.settings', $role))
            return view('errors/404');

        $pt = getPermissionTypes();

        return view('manage/settings/role/edit', compact('role', 'pt'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $role = Role2::findorFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.settings', $role))
            return view('errors/404');

        $role_request = request()->only('name', 'description');

        //dd(request()->all());
        $role->update($role_request);

        $permissions = Permission2::all();

        // Update Permissions
        $role->detachAllPermissions();
        foreach ($permissions as $permission) {
            if (request("p$permission->id"))
                $role->attachPermission($permission->id, request("p$permission->id"), Auth::user()->company_id);
        }

        Toastr::success("Saved changes");

        // Get Permissions
        $pt = getPermissionTypes();

        return view('manage/settings/role/edit', compact('role', 'pt'));
    }

    /**
     * Delete the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role2::findorFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.settings', $role))
            return json_encode("failed");

        //dd($role->id);
        $role->delete();

        return json_encode('success');
    }

    /**
     * Update role to make default 'Child' External
     */
    public function childRole(Request $request, $id)
    {
        // Set new Primary
        $new = Role2::findorFail($id);
        $new->external = 1 - $new->external;
        $new->save();

        return redirect('/settings/role');
    }

    /**
     * Update role to make default 'Child' Primary
     */
    public function childPrimary(Request $request, $id)
    {
        // Clear Old Primary
        $old = Role2::where('company_id', Auth::user()->company_id)->where('child', 'primary')->first();
        if ($old) {
            $old->child = '';
            $old->save();
        }

        // Set new Primary
        $new = Role2::findorFail($id);
        $new->child = 'primary';
        $new->save();

        return redirect('/settings/role');
    }

    /**
     * Update role to make default 'Child' default
     */
    public function childDefault(Request $request, $id)
    {

        // Clear Old Primary
        $old = Role2::where('company_id', Auth::user()->company_id)->where('child', 'default')->first();
        if ($old) {
            $old->child = '';
            $old->save();
        }

        // Set new Primary
        $new = Role2::findorFail($id);
        $new->child = 'default';
        $new->save();

        return redirect('/settings/role');
    }

    public function show(Request $request)
    {
        //
        dd('show');
    }


    public function parent(Request $request)
    {
        return view('manage/settings/role/edit_parent');
    }

    public function child(Request $request)
    {
        return view('manage/settings/role/edit_child');
    }


}
