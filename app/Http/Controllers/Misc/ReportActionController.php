<?php

namespace App\Http\Controllers\Misc;

use Illuminate\Http\Request;
use DB;
use Session;
use App\Models\Site\SiteHazardAction;
use App\Models\Site\SiteAsbestosAction;
use App\Http\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


/**
 * Class ReportActionController
 * @package App\Http\Controllers
 */
class ReportActionController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $type, $id)
    {
        // Only Allow Ajax requests
        //if ($request->ajax()) {

        if ($type == 'site.hazard') {
            $table = 'site_asbestos_actions';
            $report_id = 'asbestos_id';
        }
        if ($type == 'site.asbestos') {
            $table = 'site_asbestos_actions';
            $report_id = 'asbestos_id';
        }

        $actions = DB::table("$table AS r")->select([
            'r.id', 'r.created_by', 'r.action',
            DB::raw('DATE_FORMAT(r.created_at,\'%d/%m/%y\') AS niceDate '),
            DB::raw('CONCAT(users.firstname, " ", users.lastname) AS fullname')])
            ->join('users', 'r.created_by', '=', 'users.id')
            ->where($report_id, '=', $id)->orderBy('r.created_at')->get();

        return $actions;

        //}

        return view('errors/404');
    }

    /**
     * Store a newly created resource in storage via ajax.
     */
    public function store(Request $request, $type)
    {
        $record_request = $request->all();
        if ($request->ajax()) {
            if ($type == 'site.asbestos') {
                $record_request['asbestos_id'] = $record_request['report_id'];
                $action = SiteAsbestosAction::create($record_request);
                if ($record_request['action'] != 'Created Notification')
                    $action->emailAction($record_request['action']);
            }
            return $action;
        }

        return view('errors/404');
    }

    /**
     * Update the specified resource in storage via ajax.
     */
    public function update(Request $request, $type, $id)
    {
        $record_request = $request->all();
        if ($request->ajax()) {
            if ($type == 'site.asbestos')
                $action = SiteAsbestosAction::findOrFail($id);
        }

        $action->update($record_request);
        //$action->emailAction($action);
        return $action;
    }
}
