<?php

namespace App\Http\Controllers\Misc;

use Illuminate\Http\Request;
use Validator;

use DB;
use Session;
use App\Models\Misc\Equipment\Equipment;
use App\Models\Misc\Equipment\EquipmentLocation;
use App\Models\Misc\Equipment\EquipmentLocationItem;
use App\Models\Misc\Equipment\EquipmentLost;
use App\Models\Misc\Equipment\EquipmentLog;
use App\Models\Site\Site;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use nilsenj\Toastr\Facades\Toastr;
use Alert;

class EquipmentController extends Controller {

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
    public function inventory()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.equipment'))
            return view('errors/404');

        return view('misc/equipment/inventory');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function stocktake($id)
    {
        $location = EquipmentLocation::find($id);

        foreach (EquipmentLocation::all() as $loc) {
            if ($loc->site_id)
                $locations[$loc->id] = $loc->site->name;
            else
                $locations[$loc->id] = $loc->other;

        }
        $locations = array_unique($locations);
        asort($locations);

        $items = [];
        if (!$location)
            $locations = ['' => 'Select location'] + $locations;
        else
            $items_all = ($location->site_id) ? EquipmentLocation::where('site_id', $location->site_id)->get() : EquipmentLocation::where('other', $location->other)->get();

        $items = $items_all->filter(function($item)
        {
            if ($item->equipment->status)
                return $item;
        });

        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasPermission2('edit.equipment'))
            return view('errors/404');

        return view('misc/equipment/stocktake', compact('location', 'locations', 'items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.equipment'))
            return view('errors/404');

        return view('misc/equipment/create');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $equip = Equipment::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('view.equipment', $equip))
            return view('errors/404');

        return view('misc/equipment/show', compact('equip'));
    }

    /**
     * Edit the form
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = Equipment::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.equipment') && Auth::user()->company_id == $item->company_id)
            return view('errors/404');

        return view('misc/equipment/edit', compact('item'));
    }

    /**
     * Transfer the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function transfer($id)
    {
        $item = EquipmentLocationItem::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.equipment', $item))
            return view('errors/404');

        return view('misc/equipment/transfer', compact('item'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.equipment'))
            return view('errors/404');

        request()->validate(['name' => 'required']); // Validate

        // Create Item
        $equip_request = request()->all();
        $equip_request['company_id'] = Auth::user()->company_id;
        $equip = Equipment::create($equip_request);

        // Create New Transaction for log
        $trans = new EquipmentLog(['equipment_id' => $equip->id, 'action' => 'N', 'notes' => 'Created item']);
        $equip->log()->save($trans);

        Toastr::success("Created item");

        return redirect('/equipment/inventory');
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $equip = Equipment::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.equipment') && Auth::user()->company_id == $equip->company_id)
            return view('errors/404');

        request()->validate(['name' => 'required']); // Validate

        // Update Equipment
        $equip->update(request()->all());

        // Purchase new items
        if (request('action') == "P") {
            $qty = request('purchase_qty');
            $store = EquipmentLocation::where('site_id', 25)->first();
            // Create Store if not existing
            if (!$store) {
                $store = new EquipmentLocation(['site_id' => 25]);
                $store->save();
            }

            // Allocate New Item to Store
            $existing = EquipmentLocationItem::where('location_id', $store->id)->where('equipment_id', $equip->id)->first();
            if ($existing) {
                $existing->qty = $existing->qty + $qty;
                $existing->save();
            } else
                $store->items()->save(new EquipmentLocationItem(['location_id' => $store->id, 'equipment_id' => $equip->id, 'qty' => $qty]));

            // Update Purchased Qty
            $equip->purchased = $equip->purchased + $qty;
            $equip->save();

            // Update log
            $log = new EquipmentLog(['equipment_id' => $equip->id, 'qty' => $qty, 'action' => 'P']);
            $log->notes = 'Purchased ' . $qty . ' items';
            $equip->log()->save($log);
        }

        Toastr::success("Saved changes");

        return redirect("/equipment/inventory");
    }

    /**
     * Transfer the item to new location.
     *
     * @return \Illuminate\Http\Response
     */
    public function transferItem($id)
    {
        $item = EquipmentLocationItem::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.equipment', $item))
            return view('errors/404');

        $rules = ['type' => 'required', 'site_id' => 'required_if:type,site', 'other' => 'required_if:type,other', 'reason' => 'required_if:type,dispose'];
        $mesg = [
            'type.required'      => 'The transfer to field is required',
            'site.required_if'   => 'The site field is required',
            'other.required_if'  => 'The other location field is required',
            'reason.required_if' => 'The reason field is required',
        ];
        request()->validate($rules, $mesg); // Validate

        $old_location = $item->location->name;
        $qty = request('qty');

        // Create New Transaction for log
        $log = new EquipmentLog(['equipment_id' => $item->equipment_id, 'qty' => $qty, 'action' => 'T']);

        // Move items to New location
        if (request('type') == "dispose") { // Dispose
            $log->action = 'D';
            $log->notes = "Disposed $qty items from $old_location - " . request('reason');
            $item->equipment->disposed = $item->equipment->disposed + $qty;
            $item->equipment->save();
        } elseif (request('type') == "missing") { // Missing item
            $log->action = 'M';
            $log->notes = "Missing $qty items from $old_location ";

            // Create Lost item
            $newLost = new EquipmentLost(['location_id' => $item->location_id, 'equipment_id' => $item->equipment_id, 'qty' => $qty]);
            $newLost->save();
        } else {
            if (request('type') == "site") { //  Site
                $site = Site::find(request('site_id'));
                $log->notes = "Transferred $qty items from $old_location => $site->suburb ($site->name)";
                $location = EquipmentLocation::where('site_id', $site->id)->first();
            } elseif (request('type') == "other") {  // Other
                $log->notes = "Transferred $qty items from $old_location => " . request('other');
                $location = EquipmentLocation::where('other', request('other'))->first();
            }

            // Check if location exists
            if ($location) {
                // Check if location also has existing item to add qty to.
                $existing = EquipmentLocationItem::where('location_id', $location->id)->where('equipment_id', $item->equipment_id)->first();
                if ($existing) {
                    $existing->qty = $existing->qty + $qty;
                    $existing->save();
                } else
                    $location->items()->save(new EquipmentLocationItem(['location_id' => $location->id, 'equipment_id' => $item->equipment_id, 'qty' => $qty]));
            } else {
                // Create location + add item
                $loc_request = (request('type') == "site") ? ['site_id' => $site->id] : ['other' => request('other')];
                $newLocation = new EquipmentLocation($loc_request);
                $newLocation->save();
                $newLocation->items()->save(new EquipmentLocationItem(['location_id' => $newLocation->id, 'equipment_id' => $item->equipment_id, 'qty' => $qty]));
            }
        }

        $item->equipment->log()->save($log); // update log

        // Subtract items from current location
        $new_qty = $item->qty - $qty;
        if ($new_qty) {
            $item->qty = $item->qty - $qty;
            $item->save();
        } else
            $item->delete();

        Toastr::success("Saved changes");

        return redirect("/equipment/");
    }

    /**
     * Delete the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Equipment::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2("del.equipment", $item))
            return view('errors/404');

        $item->status = 0;
        $item->save();
        Toastr::error("Deleted item");

        return redirect("/equipment/inventory");
    }


    /**
     * Get Allocations + Process datatables ajax request.
     */
    public function getAllocation()
    {
        $items_list = (request('equipment_id')) ? [request('equipment_id')] : EquipmentLocationItem::where('company_id', Auth::user()->company_id)->pluck('equipment_id')->toArray();

        if (request('site_id'))
            $items = EquipmentLocationItem::select([
                'equipment_location_items.id', 'equipment_location_items.location_id', 'equipment_location_items.equipment_id', 'equipment_location_items.qty', 'equipment_location_items.company_id',
                'equipment_location.site_id', 'equipment_location.other', 'equipment_location.status',
                'equipment.name AS itemname', 'equipment.status', 'sites.name AS sitename', 'sites.code', 'sites.suburb'])
                ->join('equipment', 'equipment_location_items.equipment_id', '=', 'equipment.id')
                ->join('equipment_location', 'equipment_location_items.location_id', '=', 'equipment_location.id')
                ->leftjoin('sites', 'equipment_location.site_id', '=', 'sites.id')
                ->whereIn('equipment_location_items.equipment_id', $items_list)
                ->where('equipment.status', 1)
                ->where('equipment_location.site_id', request('site_id'));
        else {
            $items = EquipmentLocationItem::select([
                'equipment_location_items.id', 'equipment_location_items.location_id', 'equipment_location_items.equipment_id', 'equipment_location_items.qty', 'equipment_location_items.company_id',
                'equipment_location.site_id', 'equipment_location.other', 'equipment_location.status',
                'equipment.name AS itemname', 'equipment.status', 'sites.name AS sitename', 'sites.code', 'sites.suburb'])
                ->join('equipment', 'equipment_location_items.equipment_id', '=', 'equipment.id')
                ->join('equipment_location', 'equipment_location_items.location_id', '=', 'equipment_location.id')
                ->leftjoin('sites', 'equipment_location.site_id', '=', 'sites.id')
                ->where('equipment.status', 1)
                ->whereIn('equipment_location_items.equipment_id', $items_list);
        }

        $dt = Datatables::of($items)
            ->addColumn('view', function ($item) {
                return '<div class="text-center"><a href="/equipment/' . $item->equipment_id . '"><i class="fa fa-search"></i></a></div>';
            })
            ->editColumn('qty', function ($item) {
                return ($item->equipment->total) ? "$item->qty / " . $item->equipment->total : 0;
            })
            ->editColumn('code', function ($item) {
                return ($item->location->site_id) ? $item->location->site->code : '-';
            })
            ->editColumn('suburb', function ($item) {
                return ($item->location->site_id) ? $item->location->site->suburb : '-';
            })
            ->editColumn('sitename', function ($item) {
                return ($item->location->site_id) ? $item->location->site->name : '-';
            })
            ->addColumn('action', function ($item) {
                return (Auth::user()->allowed2('edit.equipment', $item)) ? '<a href="/equipment/' . $item->id . '/transfer" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom">Transfer</a>' : '';
            })
            ->rawColumns(['view', 'created_by', 'action'])
            ->make(true);

        return $dt;
    }

    /**
     * Get Missing + Process datatables ajax request.
     */
    public function getMissing()
    {

        $items = EquipmentLost::select([
            'equipment_lost.id', 'equipment_lost.location_id', 'equipment_lost.equipment_id', 'equipment_lost.qty', 'equipment_lost.company_id',
            'equipment_location.site_id', 'equipment_location.other', 'equipment_location.status',
            'equipment.name AS itemname', 'sites.name AS sitename', 'sites.code', 'sites.suburb'])
            ->join('equipment', 'equipment_lost.equipment_id', '=', 'equipment.id')
            ->join('equipment_location', 'equipment_lost.location_id', '=', 'equipment_location.id')
            ->leftjoin('sites', 'equipment_location.site_id', '=', 'sites.id')
            ->where('equipment_lost.equipment_id', request('equipment_id'));

        $dt = Datatables::of($items)
            ->editColumn('qty', function ($item) {
                return ($item->equipment->total) ? "$item->qty / " . $item->equipment->total : 0;
            })
            ->editColumn('code', function ($item) {
                return ($item->location->site_id) ? $item->location->site->code : '-';
            })
            ->editColumn('suburb', function ($item) {
                return ($item->location->site_id) ? $item->location->site->suburb : '-';
            })
            ->editColumn('sitename', function ($item) {
                return ($item->location->site_id) ? $item->location->site->name : '-';
            })
            ->addColumn('action', function ($item) {
                return (Auth::user()->allowed2('edit.equipment', $item)) ? '<a href="/equipment/' . $item->id . '/transfer" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom">Transfer</a>' : '';
            })
            ->rawColumns(['view', 'created_by', 'action'])
            ->make(true);

        return $dt;
    }

    /**
     * Get Equipment Inventory + Process datatables ajax request.
     */
    public function getInventory()
    {
        $equipment = Equipment::where('company_id', Auth::user()->company_id)->where('status', 1);

        $dt = Datatables::of($equipment)
            ->editColumn('id', function ($equip) {
                return '<div class="text-center"><a href="/equipment/' . $equip->id . '"><i class="fa fa-search"></i></a></div>';
            })
            ->addColumn('total', function ($equip) {
                $str = $equip->total;
                if ($equip->total > ($equip->purchased - $equip->disposed))
                    $str = "<span class='label label-warning'>$equip->total</span>";
                return $str;
            })
            ->addColumn('lost', function ($equip) {
                return ($equip->total_lost) ? $equip->total_lost : '-';
            })
            ->addColumn('action', function ($equip) {
                return '<a href="/equipment/' . $equip->id . '/edit" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom"><i class="fa fa-pencil"></i> Edit</a>';
            })
            ->rawColumns(['id', 'total', 'action'])
            ->make(true);

        return $dt;
    }

    /**
     * Get Transaction History + Process datatables ajax request.
     */
    public function getLog()
    {
        $transactions = EquipmentLog::where('equipment_id', request('equipment_id'));

        $dt = Datatables::of($transactions)
            ->editColumn('created_at', function ($trans) {
                return $trans->created_at->format('d/m/Y');
            })
            ->editColumn('created_by', function ($trans) {
                return $trans->user->name;
            })
            ->rawColumns(['id', 'created_by'])
            ->make(true);

        return $dt;
    }
}
