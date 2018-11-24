<?php

namespace App\Http\Controllers\Misc;

use Illuminate\Http\Request;
use Validator;

use DB;
use Session;
use App\Models\Misc\Equipment\Equipment;
use App\Models\Misc\Equipment\EquipmentLocation;
use App\Models\Misc\Equipment\EquipmentLocationItem;
use App\Models\Misc\Equipment\EquipmentStocktake;
use App\Models\Misc\Equipment\EquipmentStocktakeItem;
use App\Models\Misc\Equipment\EquipmentLost;
use App\Models\Misc\Equipment\EquipmentLog;
use App\Models\Site\Site;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use nilsenj\Toastr\Facades\Toastr;
use Alert;

class EquipmentStocktakeController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasAnyPermissionType('equipment'))
            return view('errors/404');

        return view('misc/equipment/list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $location = EquipmentLocation::find($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasPermission2('edit.equipment'))
            return view('errors/404');

        foreach (EquipmentLocation::where('company_id', Auth::user()->company_id)->get() as $loc) {
            if (count($loc->items))
                $locations[$loc->id] = $loc->name;
        }
        asort($locations);

        $locations = ['' => 'Select location'] + $locations;
        $items = [];
        if ($location) {
            // Get items then filter out 'deleted'
            $all_items = EquipmentLocationItem::where('location_id', $location->id)->get();
            $items = $all_items->filter(function ($item) {
                if ($item->equipment->status) return $item;
            });
        }



        return view('misc/equipment/stocktake', compact('location', 'locations', 'items'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function foundItem($id)
    {
        $location = EquipmentLocation::find($id);

        // Check authorisation and throw 404 if not
        //if (!Auth::user()->hasPermission2('edit.equipment'))
        //    return view('errors/404');


        //$items = EquipmentLocationItem::where('location_id', $location->id)->get();

        return view('misc/equipment/stocktake-found', compact('location'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showStocktake($id)
    {
        $stock = EquipmentStocktake::find($id);

        // Check authorisation and throw 404 if not
        //if (!Auth::user()->hasPermission2('edit.equipment'))
        //    return view('errors/404');


        //$items = EquipmentLocationItem::where('location_id', $location->id)->get();

        return view('misc/equipment/stocktake-show', compact('stock'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $location = EquipmentLocation::findOrFail($id);
        $extra_items = [];

        $stocktake = new EquipmentStocktake(['location_id' => $location->id]);
        $stocktake->save();
        $passed_all = 1;;

        // Get items then filter out 'deleted'
        $all_items = EquipmentLocationItem::where('location_id', $location->id)->get();
        $items = $all_items->filter(function ($item) {
            if ($item->equipment->status) return $item;
        });
        // Check if current qty matches DB
        foreach ($items as $item) {
            $qty_now = request($item->id . '-qty');
            $passed_item = 1;
            $stocktake_item = new EquipmentStocktakeItem(['stocktake_id' => $stocktake->id, 'equipment_id' => $item->equipment_id, 'qty_expect' => $item->qty, 'qty_actual' => $qty_now]);
            if ($item->qty > $qty_now) {
                // Missing items
                $passed_all = $pass_item = 0;
                $this->lostItem($item->location_id, $item->equipment_id, ($item->qty - $qty_now));
            } elseif ($item->qty < $qty_now) {
                // Extra items
                $item->extra = ($qty_now - $item->qty);
                //$this->foundItem($item->location_id, $item->equipment_id, ($qty_now - $item->qty));
                $extra_items[$item->equipment_id] = ($qty_now - $item->qty);
            }


            // Update altered qty at location
            if ($item->qty != $qty_now) {
                if ($qty_now) {
                    $item->qty = $qty_now;
                    $item->save();
                } else
                    $item->delete();
            }

            // Save Stocktake
            $stocktake_item->passed = $passed_item;
            $stocktake_item->save();
        }

        $stocktake->passed = $passed_all;
        $stocktake->save();

        // Add extra items to location
        for ($i = 1; $i <= 10; $i ++) {
            if (request("$i-extra_qty") && request("$i-extra_id")) {
                $equip = Equipment::findOrFail(request("$i-extra_id"));
                $extra_items[$equip->id] = request("$i-extra_qty");

                // Add item to location
                $location->items()->save(new EquipmentLocationItem(['location_id' => $location->id, 'equipment_id' => $equip->id, 'qty' => request("$i-extra_qty"), 'extra' => request("$i-extra_qty")]));

                // Add item to stocktake
                $stocktake_item = new EquipmentStocktakeItem(['stocktake_id' => $stocktake->id, 'equipment_id' => $equip->id, 'qty_expect' => 0, 'qty_actual' => request("$i-extra_qty"), 'passed' => 1]);
                $stocktake_item->save();
            }
        }

        if (count($extra_items)) {
            $lost_items = EquipmentLost::whereIn('equipment_id', $extra_items)->get();
            if ($lost_items)
                return redirect("/equipment/stocktake/found/$location->id");
            //return view('misc/equipment/stocktake-lost', compact('location', 'extra_items', 'lost_items'));
        }
        if (!$passed_all)
            Toastr::error("Some items marked as missing");
        Toastr::success("Saved changes");


        return redirect("/equipment/stocktake/$location->id");
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function transferLost($id)
    {
        $location = EquipmentLocation::findOrFail($id);

        $transfer = [];
        $verify = [];
        foreach (request()->all() as $key => $val) {
            if (substr($key, 0, 3) == 'qty') {
                list($item_id, $qty) = explode('-', substr($key, 4));
                $verify[$item_id]['max'] = $qty;
                $verify[$item_id]['now'] = (isset($verify[$item_id]['now'])) ? $verify[$item_id]['now'] + $val : $val;
                if ($val)
                    $transfer[$item_id] = $val;
            }
        }

        // Verify not trying to transfer more 'lost' item to new location then there are
        foreach ($verify as $id => $val) {
            if ($val['now'] > $val['max']) {
                Toastr::error("Transfer failed");
                $equipment = Equipment::find($id);

                return back()->withErrors(['exceeded_lost' => "Attempted to transfer " . $val['now'] . " $equipment->name and the location only has an addition " . $val['max']])->withInput();
            }
        }

        // Mark lost item as found
        foreach ($transfer as $item_id => $qty) {
            $lost = EquipmentLost::find($item_id);
            $found = EquipmentLocationItem::where('location_id', $location->id)->where('equipment_id', $lost->equipment_id)->first();
            //echo "Transfer ".$lost->equipment->name."[$lost->equipment_id] From:".$lost->location->name." To: $location->name Item: Qty:$qty<br>";

            $found->extra = $found->extra - $qty;
            $found->save();

            $lost->qty = $lost->qty - $qty;
            ($lost->qty) ? $lost->save() : $lost->delete();

            // Create New Transaction for log
            $log = new EquipmentLog(['equipment_id' => $lost->equipment_id, 'qty' => $qty, 'action' => 'F', 'notes' => "Found $qty missing items from " . $lost->location->name . " @ $location->name"]);
            $log->save();
        }
        //dd(request()->all());
        Toastr::success("Saved changes");

        return redirect("/equipment/stocktake/$location->id");
    }


    /**
     * Lost item
     *
     * @return \Illuminate\Http\Response
     */
    public function lostItem($location_id, $equipment_id, $qty)
    {
        $location = EquipmentLocation::findOrFail($location_id);

        $existing = EquipmentLost::where('location_id', $location_id)->where('equipment_id', $equipment_id)->first();
        if ($existing) {
            // Update existing lost qty
            $existing->qty = $existing->qty + $qty;
            $existing->save();
        } else {
            // Create Lost item
            $newLost = new EquipmentLost(['location_id' => $location_id, 'equipment_id' => $equipment_id, 'qty' => $qty]);
            $newLost->save();
        }

        // Create New Transaction for log
        $log = new EquipmentLog(['equipment_id' => $equipment_id, 'qty' => $qty, 'action' => 'M', 'notes' => "Missing $qty items from $location->name"]);
        $log->save();
    }

    /**
     * Get Stocktake + Process datatables ajax request.
     */
    public function getStocktake()
    {
        //dd(request('location_id'));
        $stocktake = EquipmentStocktake::where('location_id', request('location_id'));

        $dt = Datatables::of($stocktake)
            ->editColumn('id', function ($stock) {
                return '<div class="text-center"><a href="/equipment/stocktake/view/' . $stock->id . '"><i class="fa fa-search"></i></a></div>';
            })
            ->editColumn('created_at', function ($stock) {
                return $stock->created_at->format('d/m/Y');
            })
            ->editColumn('created_by', function ($stock) {
                return $stock->user->name;
            })
            ->addColumn('summary', function ($stock) {
                return ($stock->summary());
            })
            ->editColumn('passed', function ($stock) {
                return ($stock->passed) ? 'Yes' : 'No';
            })
            ->rawColumns(['id', 'created_by', 'summary'])
            ->make(true);

        return $dt;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        //
    }
}
