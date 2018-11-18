<?php

namespace App\Http\Controllers\Misc;

use Illuminate\Http\Request;
use Validator;

use DB;
use Session;
use App\Models\Misc\Equipment;
use App\Models\Misc\EquipmentLocation;
use App\Models\Misc\EquipmentTransaction;
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
        $item = Equipment::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('view.equipment', $item))
            return view('errors/404');

        return view('misc/equipment/show', compact('item'));
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
        $location = EquipmentLocation::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.equipment', $location))
            return view('errors/404');

        return view('misc/equipment/transfer', compact('location'));
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
        $item_request = request()->all();
        $item_request['company_id'] = Auth::user()->company_id;
        $item = Equipment::create($item_request);

        // Create New Transaction for log
        $trans = new EquipmentTransaction(['item_id' => $item->id, 'action' => 'N', 'notes' => 'Created item', 'company_id' => Auth::user()->company_id]);
        $item->transactions()->save($trans);

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
        $item = Equipment::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.equipment') && Auth::user()->company_id == $item->company_id)
            return view('errors/404');

        request()->validate(['name' => 'required', 'reason' => 'required_if:action,D'], ['reason.required_if' => 'The reason field is required']); // Validate
        $item_request = request()->all();

        // Update Quantity if Purchase or Dispose
        if (request('action') == "P") {
            $trans = new EquipmentTransaction(['item_id' => $item->id, 'action' => request('action'), 'company_id' => Auth::user()->company_id]); // Create transaction for log

            $item_request['qty'] = $item->qty + request('purchase_qty');
            $trans->qty = request('purchase_qty');
            $trans->notes = 'Purchased ' . request('purchase_qty') . ' items';

            // Allocate New Item to Store
            $existing = EquipmentLocation::where('item_id', $item->id)->where('site_id', 25)->first();
            if ($existing) {
                $existing->qty = $existing->qty + request('purchase_qty');
                $existing->save();
            } else {
                $location = new EquipmentLocation(['item_id' => $item->id, 'site_id' => 25, 'qty' => request('purchase_qty'), 'company_id' => Auth::user()->company_id]);
                $item->locations()->save($location);
            }
            $item->transactions()->save($trans); // save transaction
        }

        //dd($item_request);
        $item->update($item_request);
        Toastr::success("Saved changes");

        return redirect("/equipment/inventory");
    }

    /**
     * Transfer the sitem to new location.
     *
     * @return \Illuminate\Http\Response
     */
    public function transferItem($id)
    {
        $location = EquipmentLocation::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.equipment', $location))
            return view('errors/404');

        $rules = ['type' => 'required', 'site_id' => 'required_if:type,site', 'other' => 'required_if:type,other', 'reason' => 'required_if:type,dispose'];
        $mesg = [
            'type.required'      => 'The transfer to field is required',
            'site.required_if'   => 'The site field is required',
            'other.required_if'  => 'The other location field is required',
            'reason.required_if' => 'The reason field is required',
        ];
        request()->validate($rules, $mesg); // Validate

        $old_location = ($location->site_id) ? $location->site->suburb . ' (' . $location->site->name . ')' : $location->other;

        // Create New Transaction for log
        $trans = new EquipmentTransaction(['item_id' => $location->item_id, 'action' => 'T', 'company_id' => Auth::user()->company_id]);
        $trans->qty = request('qty');

        // Move items to New location
        if (request('type') == "site") { //  Site
            $site = Site::find(request('site_id'));
            $trans->notes = 'Transferred ' . request('qty') . " items from $old_location => $site->suburb ($site->name)";

            $existing = EquipmentLocation::where('item_id', $location->item_id)->where('site_id', $site->id)->first();
            if ($existing) {
                $existing->qty = $existing->qty + request('qty');
                $existing->save();
            } else {
                $newLocation = new EquipmentLocation(['item_id' => $location->item_id, 'site_id' => $site->id, 'qty' => request('qty'), 'company_id' => Auth::user()->company_id]);
                $location->item->locations()->save($newLocation);
            }
        } elseif (request('type') == "other") {  // Other
            $trans->notes = 'Transferred ' . request('qty') . " items from $old_location => " . request('other');

            $existing = EquipmentLocation::where('item_id', $location->item_id)->where('other', request('other'))->first();
            if ($existing) {
                $existing->qty = $existing->qty + request('qty');
                $existing->save();
            } else {
                $newLocation = new EquipmentLocation(['item_id' => $location->item_id, 'other' => request('other'), 'qty' => request('qty'), 'company_id' => Auth::user()->company_id]);
                $location->item->locations()->save($newLocation);
            }
        } elseif (request('type') == "dispose") { // Dispose
            $trans->action = 'D';
            $trans->notes = 'Disposed ' . request('qty') . " items from $old_location - " . request('reason');
        }

        $location->item->transactions()->save($trans);

        // Subtract items from current location
        $new_qty = $location->qty - request('qty');
        if ($new_qty) {
            $location->qty = $location->qty - request('qty');
            $location->save();
        } else
            $location->delete();

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

        $item->delete();
        Toastr::error("Deleted item");

        return redirect("/equipment/inventory");
    }


    /**
     * Get Allocations + Process datatables ajax request.
     */
    public function getAllocation()
    {
        $items = (request('item_id')) ? [request('item_id')] : EquipmentLocation::where('company_id', Auth::user()->company_id)->pluck('item_id')->toArray();

        $locations = EquipmentLocation::select([
            'equipment_location.id', 'equipment_location.item_id', 'equipment_location.site_id', 'equipment_location.other', 'equipment_location.qty', 'equipment_location.company_id',
            'equipment.name AS itemname', 'equipment.qty AS total',
            'sites.name AS sitename', 'sites.code', 'sites.suburb'])
            ->join('equipment', 'equipment_location.item_id', '=', 'equipment.id')
            ->leftjoin('sites', 'equipment_location.site_id', '=', 'sites.id')
            ->whereIn('equipment_location.item_id', $items);

        $dt = Datatables::of($locations)
            ->addColumn('view', function ($loc) {
                return '<div class="text-center"><a href="/equipment/' . $loc->item_id . '"><i class="fa fa-search"></i></a></div>';
            })
            ->editColumn('qty', function ($loc) {
                return ($loc->item->total) ? "$loc->qty / " . $loc->item->total : 0;
            })
            ->editColumn('code', function ($loc) {
                return ($loc->site_id) ? $loc->site->code : '-';
            })
            ->editColumn('suburb', function ($loc) {
                return ($loc->site_id) ? $loc->site->suburb : '-';
            })
            ->editColumn('sitename', function ($loc) {
                return ($loc->site_id) ? $loc->site->name : '-';
            })
            ->addColumn('action', function ($loc) {
                return (Auth::user()->allowed2('edit.equipment', $loc)) ? '<a href="/equipment/' . $loc->id . '/transfer" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom">Transfer</a>' : '';
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
        $equipment = Equipment::where('company_id', Auth::user()->company_id);

        $dt = Datatables::of($equipment)
            ->editColumn('id', function ($item) {
                return '<div class="text-center"><a href="/equipment/' . $item->id . '"><i class="fa fa-search"></i></a></div>';
            })
            ->addColumn('location', function ($item) {
                return $item->locationsSBC();
            })
            ->addColumn('total', function ($item) {
                return $item->total;
            })
            ->addColumn('action', function ($item) {
                return '<a href="/equipment/' . $item->id . '/edit" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom"><i class="fa fa-pencil"></i> Edit</a>';
            })
            ->rawColumns(['id', 'location', 'action'])
            ->make(true);

        return $dt;
    }

    /**
     * Get Transaction History + Process datatables ajax request.
     */
    public function getTransactions()
    {
        $transactions = EquipmentTransaction::where('item_id', request('item_id'));

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
