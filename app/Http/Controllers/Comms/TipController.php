<?php

namespace App\Http\Controllers\Comms;

use Illuminate\Http\Request;
use DB;
use Session;
use App\Http\Requests;
use App\Models\Comms\SafetyTip;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


/**
 * Class TipController
 * @package App\Http\Controllers\Safety
 */
class TipController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasAnyPermissionType('safetytip'))
            return view('errors/404');

        if ($request->ajax()) {
            $tips = SafetyTip::select([
                'safety_tips.id', 'safety_tips.title', 'safety_tips.body', 'safety_tips.status',
                DB::raw('DATE_FORMAT(last_published,\'%d/%m/%y\') AS niceDate '),
                DB::raw('CONCAT(users.firstname, " ", users.lastname) AS fullname')])
                ->join('users', 'safety_tips.created_by', '=', 'users.id')
                ->where('safety_tips.company_id', Auth::user()->company->reportsTo()->id)
                ->orderBy('last_published', 'DESC')->get();

            return $tips;
        }

        return view('comms/safetytip/list');
    }

    /**
     * Store a newly created resource in storage via ajax.
     */
    public function store(Request $request)
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.safetytip'))
            return view('errors/404');

        if ($request->ajax()) {
            return SafetyTip::create($request->all());
        }

        return view('errors/404');
    }

    /**
     * Update the specified resource in storage via ajax.
     */
    public function update(Request $request, $id)
    {
        $tip = SafetyTip::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.safetytip', $tip))
            return view('errors/404');

        if ($request->ajax()) {
            if (Auth::user()->company->reportsTo()->id == $tip->company_id) {
                $tip->update($request->all());

                return $tip;
            }
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
        $tip = SafetyTip::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('del.safetytip', $tip))
            return view('errors/404');

        $tip->delete();

        return json_encode('success');
    }

    public function getActive()
    {
        $tip = SafetyTip::where('status', '1')->first();

        return $tip;
    }

    public function show(Request $request, $id)
    {
        // Empty function required to stop error occurring for absent function.
    }
}
