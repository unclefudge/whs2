<?php

namespace App\Http\Controllers\Support;

use Illuminate\Http\Request;
use DB;
use Session;
use App\Models\Support\SupportTicketAction;
use App\Http\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


/**
 * Class SiteHazardController
 * @package App\Http\Controllers
 */
class SupportTicketActionController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // Only Allow Ajax requests
        //if ($request->ajax()) {
        $actions = DB::table('support_tickets_actions AS a')
            ->select(['a.id', 'a.created_by', 'a.action',
                DB::raw('DATE_FORMAT(a.created_at,\'%d/%m/%y\') AS niceDate '),
                DB::raw('CONCAT(users.firstname, " ", users.lastname) AS fullname')])
            ->join('users', 'a.created_by', '=', 'users.id')
            ->where('ticket_id', '=', $id)->orderBy('a.created_at')->get();

        return $actions;

        //}

        return view('errors/404');
    }

    /**
     * Store a newly created resource in storage via ajax.
     */
    public function store(Request $request)
    {
        if ($request->ajax()) {
            $action = SupportTicketAction::create($request->all());
            //$action->emailAction($action);

            return $action;
        }

        return view('errors/404');
    }

    /**
     * Update the specified resource in storage via ajax.
     */
    public function update(Request $request, $id)
    {
        $action = SupportTicketAction::findOrFail($id);
        $action->update($request->all());

        return $action;
    }
}
