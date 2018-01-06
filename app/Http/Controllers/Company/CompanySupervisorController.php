<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use Validator;

use DB;
use App\Models\Company\CompanySupervisor;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use nilsenj\Toastr\Facades\Toastr;
use Carbon\Carbon;

class CompanySupervisorController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasAnyPermissionType('area.super'))
            return view('errors/404');

        return view('site/supervisor/list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check authorisation and throw 404 if not
        //if (!Auth::user()->hasPermission2('edit.area.super'))
        //    return view('errors/404');

        //return view('site/create');
    }

    /**
     * Store a newly created resource in storage via ajax.
     */
    public function store(Request $request)
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasPermission2('edit.area.super'))
            return view('errors/404');

        if ($request->ajax()) {
            return CompanySupervisor::create($request->all());
        }

        return view('errors/404');
    }

    /**
     * Delete the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $super = CompanySupervisor::find($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.area.super', $super))
            return view('errors/404');

        $super = CompanySupervisor::where('id', $id)->orWhere('parent_id', $id)->delete();
        return json_encode('success');
    }


    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(SiteRequest $request, $slug)
    {
        //
    }

    /**
     * Get Current Supervisors the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSupers(Request $request)
    {
        // Current Supervisors
        $supervisors = DB::table('company_supervisors AS s')->select('s.id', 's.user_id', 's.parent_id',
            DB::raw('CONCAT(users.firstname, " ", users.lastname) AS fullname'))
            ->where('s.company_id', Auth::user()->company_id)
            ->join('users', 's.user_id', '=', 'users.id')->get();

        $supers = [];
        foreach ($supervisors as $super) {
            $supers[] = ['id' => $super->id, 'user_id' => $super->user_id, 'name' => $super->fullname, 'parent_id' => $super->parent_id, 'open' => false];
        }

        // Company Staff
        $staff = Auth::user()->company->staffStatus(1);
        $sel_staff = [];
        $sel_staff[] = ['value' => 0, 'text' => 'Select employee to add as Supervisor'];
        foreach ($staff as $user) {
            $sel_staff[] = ['value' => $user->id, 'text' => $user->firstname . ' ' . $user->lastname];
        }

        $json = [];
        $json[] = $supers;
        $json[] = $sel_staff;

        return $json;
    }


}
