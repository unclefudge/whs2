<?php

namespace App\Http\Controllers\Site\Planner;

use Illuminate\Http\Request;

use DB;
use App\Models\Site\Planner\Trade;
use App\Http\Requests;
use App\Http\Requests\Site\Planner\TradeRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TradeController extends Controller {

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $trades = Trade::select(['id', 'name', 'company_id', 'status'])
                ->where('company_id', '=', Auth::user()->company_id)
                ->get();

            foreach ($trades as $trade)
                $trade['open'] = false;

            return $trades;
        }

        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasAnyPermissionType('trade'))
            return view('errors/404');

        $trades = Trade::select(['id', 'name'])
            ->where('company_id', '=', Auth::user()->company_id)
            ->where('trades.status', '=', '1');

        return view('planner/trade/list', compact('trades'));
    }

    /**
     * Store a newly created resource in storage via ajax.
     */
    public function store(TradeRequest $request)
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasPermission2('add.trade'))
            return view('errors/404');

        if ($request->ajax()) {
            return Trade::create($request->all());
        }

        return view('errors/404');
    }

    /**
     * Update the specified resource in storage via ajax.
     */
    public function update(TradeRequest $request, $id)
    {
        if ($request->ajax()) {
            $trade = Trade::findOrFail($id);
            $trade->update($request->all());

            // If trade status set to 0 ie. disabled then also
            // need to remove trade from all companys using it??

            return $trade;
        }

        return view('errors/404');
    }

    /**
     * Show a resource in storage.
     */
    public function show(Request $request)
    {
       // Required method but not used
    }
}