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
        $roles = Role2::all()->sortBy('name');

        if (Auth::user()->company->subscription)
            return view('manage/role/list', compact('roles'));

        return view('errors/404');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //if (!Auth::user()->hasPermission2('add.role'))
        //   return view('errors/404');

        return view('manage/role/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        // Check authorisation and throw 404 if not
        //if (!(Auth::user()->hasPermission2('add.role') || Auth::user()->hasPermission2('edit.settings'))
        //    return view('errors/404');

        // Create Role
        Role::create($request->all());
        Toastr::success("Created new role");

        return redirect('manage/role');
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
        //if (!Auth::user()->allowed2('edit.role', $role)) 
        //    return view('errors/404');

        $pt = getPermissionTypes();

        return view('manage/role/edit', compact('role', 'pt'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role = Role2::findorFail($id);

        // Check authorisation and throw 404 if not
        //if (!Auth::user()->allowed2('edit.role', $role)) 
        //    return view('errors/404');

        if (Auth::user()->id == 3) {
            $permissions = Permission2::all();

            // Update Permissions
            $role->detachAllPermissions();
            foreach ($permissions as $permission) {
                if ($request->get("p$permission->id") != 0)
                    $role->attachPermission($permission->id, $request->get("p$permission->id"), Auth::user()->company_id);
            }

            Toastr::success("Saved changes");

            // Get Permissions
            $pt = getPermissionTypes();
        }

        return view('manage/role/edit', compact('role', 'pt'));
    }

    /**
     * Delete the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //
    }

    /**
     * Update role to make default 'Child' Primary
     */
    public function childPrimary(Request $request, $id)
    {


        // Clear Old Primary
        $old = Role2::where('company_id', Auth::user()->company_id)->where('model', 'primary')->first();
        if ($old) {
            $old->child = '';
            $old->save();
        }

        // Set new Primary
        $new = Role::findorFail($id);
        $new->child = 'primary';
        $new->save();

        return redirect('manage/role');
    }

    /**
     * Update role to make default 'Child' default
     */
    public function childDefault(Request $request, $id)
    {

        // Clear Old Primary
        $old = Role2::where('company_id', Auth::user()->company_id)->where('model', 'child')->first();
        if ($old) {
            $old->child = '';
            $old->save();
        }

        // Set new Primary
        $new = Role::findorFail($id);
        $new->child = 'default';
        $new->save();

        return redirect('manage/role');
    }

    public function show(Request $request)
    {
        //
    }


    public function parent(Request $request)
    {
        return view('manage/role/edit_parent');
    }

    public function child(Request $request)
    {
        return view('manage/role/edit_child');
    }


}
