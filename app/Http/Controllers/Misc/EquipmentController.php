<?php

namespace App\Http\Controllers\Misc;

use Illuminate\Http\Request;
use Validator;

use DB;
use Session;
use App\User;
use App\Models\Misc\Equipment\Equipment;
use App\Models\Misc\Equipment\EquipmentLocation;
use App\Models\Misc\Equipment\EquipmentLocationItem;
use App\Models\Misc\Equipment\EquipmentLost;
use App\Models\Misc\Equipment\EquipmentLog;
use App\Models\Comms\Todo;
use App\Models\Site\Site;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use nilsenj\Toastr\Facades\Toastr;
use Carbon\Carbon;
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
        if (!Auth::user()->hasAnyPermissionType('equipment'))
            return view('errors/404');

        return view('misc/equipment/inventory');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function writeoff()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasPermission2('edit.equipment.stocktake'))
            return view('errors/404');

        $missing = EquipmentLost::all();

        return view('misc/equipment/writeoff', compact('missing'));
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
     *
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
     * Transfer the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function transferBulk($id)
    {
        $location = EquipmentLocation::find($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.equipment.stocktake', $location))
            return view('errors/404');

        foreach (EquipmentLocation::where('status', 1)->where('notes', null)->where('site_id', '<>', '25')->get() as $loc) {
            if (count($loc->items))
                $locations[$loc->id] = $loc->name;
        }
        asort($locations);
        $locations = ['1' => 'CAPE COD STORE'] + $locations;
        if (!$location)
            $locations = ['' => 'Select location'] + $locations;

        $items = [];
        if ($location) {
            // Get items then filter out 'deleted'
            $all_items = EquipmentLocationItem::where('location_id', $location->id)->get();
            $items = $all_items->filter(function ($item) {
                if ($item->equipment->status) return $item;
            });
        }

        return view('misc/equipment/transfer-bulk', compact('location', 'locations', 'items'));
    }

    /**
     * Task the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function verifyTransfer($id)
    {
        $location = EquipmentLocation::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.equipment', $location))
            return view('errors/404');

        list ($from_id, $type, $details, $user) = explode(':', $location->notes);
        $from_location = EquipmentLocation::find($from_id);
        $from = ($from_location->site_id) ? $from_location->site->address . ', ' . $from_location->site->suburb . ' (' . $from_location->site->name . ')' : $from_location->other;
        if ($type == 'site') {
            $site = Site::find($details);
            $to = "$site->address, $site->suburb ($site->name)";
        } else
            $to = $details;

        return view('misc/equipment/transfer-verify', compact('location', 'from', 'to'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.equipment') && Auth::user()->company_id == 3)
            return view('errors/404');

        request()->validate(['name' => 'required']); // Validate

        // Create Item
        $equip_request = request()->all();
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
        $qty = request('purchase_qty');

        // Purchase new items
        if ($qty) {
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

        //dd(request()->all());
        $old_location = $item->location->name;
        $qty = request('qty');
        $site_id = (request('type') == "store" || request('type') == "site") ? request('site_id') : null;
        $other = (request('type') == "other") ? request('other') : null;

        // Move items to New location
        if (request('type') == "dispose") { // Dispose
            $item->equipment->disposed = $item->equipment->disposed + $qty;
            $item->equipment->save();
            $log = new EquipmentLog(['equipment_id' => $item->equipment_id, 'qty' => $qty, 'action' => 'D', 'notes' => "Disposed $qty items from $old_location - " . request('reason')]);
            $log->save();
            $this->subtractItems($item, $qty);
        } else {
            // Verify not transfer to/from aren't same location
            if (($item->location->site_id && $item->location->site_id == $site_id) || ($item->location->other && $item->location->other == $other))
                return back()->withErrors(['same' => "The destination location can't be the same as the originating."]);

            if (request('assign'))
                $this->assignTransfer($item, $qty, $site_id, $other, request('assign')); // Assign transfer to user
            else
                $this->performTransfer($item, $qty, $site_id, $other);  // Transfer items now
        }

        Toastr::success("Saved changes");

        return redirect("/equipment/");
    }

    /**
     * Transfer Bulk items to new location.
     *
     * @return \Illuminate\Http\Response
     */
    public function transferBulkItems($id)
    {
        $rules = ['location_id' => 'required', 'site_id' => 'required:site'];
        $mesg = ['location_id.required' => 'The transfer from field is required', 'site.required_if' => 'The transfer to field is required'];
        request()->validate($rules, $mesg); // Validate

        $location = EquipmentLocation::findOrFail($id);

        // Verify not transfer to/from aren't same location
        if ($location->site_id == request('site_id'))
            return back()->withErrors(['samelocation' => "The From and To locations can't be the same"]);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.equipment', $location))
            return view('errors/404');

        // Get items then filter out 'deleted'
        $all_items = EquipmentLocationItem::where('location_id', $location->id)->get();
        $items = $all_items->filter(function ($item) {
            if ($item->equipment->status) return $item;
        });

        //dd(request()->all());
        // Get Qty to transfer for each item at location
        foreach ($items as $item) {
            $qty = request($item->id . '-qty');
            if ($qty) {
                if (request('assign'))
                    $this->assignTransfer($item, $qty, request('site_id'), null, request('assign')); // Assign transfer to user
                else
                    $this->performTransfer($item, $qty, request('site_id'), null);  // Transfer items now
            }
        }

        Toastr::success("Saved changes");

        return redirect("/equipment/");
    }

    /**
     * Transfer the item to new location.
     *
     */
    public function confirmTransfer($id)
    {
        $location = EquipmentLocation::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.equipment', $location))
            return view('errors/404');

        list($crap, $todo_id) = explode(':', $location->other);
        list($old_location_id, $type, $details, $user_id) = explode(':', $location->notes);

        $site_id = ($type == 'site') ? $details : null;
        $other = ($type == 'other') ? $details : null;

        // Check if current qty matches DB
        foreach ($location->items as $item) {
            $qty = request($item->id . '-qty');
            //echo "checking $item->id-qty [$qty]<br>";
            if ($item->qty > $qty) {
                // There were less items found at location then expected so
                // check if 'extra' items are elsewhere and any none 'extra' mark them as missing
                if (($item->qty - $qty) > $item->equipment->total_excess)
                    $this->lostItem($item->location_id, $item->equipment_id, ($item->qty - $qty - $item->equipment->total_excess));
            }
            $this->performTransfer($item, $qty, $site_id, $other);
        }

        $location->status = 0;
        $location->save();

        $todo = Todo::find($todo_id);
        $todo->done_by = Auth::user()->id;
        $todo->done_at = Carbon::now();
        $todo->status = 0;
        $todo->save();
        Toastr::success("Transfer completed");

        return redirect("/todo/$todo->id");
    }


    /**
     * Perform actual transfer of item to new locations
     */
    public function performTransfer($item, $qty, $site_id, $other)
    {
        $old_location = $item->location->name;
        $log = new EquipmentLog(['equipment_id' => $item->equipment_id, 'qty' => $qty, 'action' => 'T']);

        if ($site_id) { //  Site
            $site = Site::find($site_id);
            $log->notes = "Transferred $qty items from $old_location => $site->suburb ($site->name)";
            $location = EquipmentLocation::where('site_id', $site_id)->first();
        } else {  // Other
            $log->notes = "Transferred $qty items from $old_location => $other";
            $location = EquipmentLocation::where('other', $other)->first();
        }
        $log->save();

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
            $loc_request = ['site_id' => $site_id, 'other' => $other];
            $newLocation = new EquipmentLocation($loc_request);
            $newLocation->save();
            $newLocation->items()->save(new EquipmentLocationItem(['location_id' => $newLocation->id, 'equipment_id' => $item->equipment_id, 'qty' => $qty]));
        }
        $this->subtractItems($item, $qty); // Subtract items from original location
    }

    /**
     * Assign transfer of items to user
     */
    public function assignTransfer($item, $qty, $site_id, $other, $assign)
    {
        //
        //  Transfer is assigned to be done by a User
        //
        $old_site = ($item->location->site_id) ? Site::find($item->location->site_id) : null;
        $old_location_full = ($old_site) ? "$old_site->address, $old_site->suburb ($old_site->name)" : $item->location->name;
        $old_location = ($old_site) ? "$old_site->suburb ($old_site->name)" : $item->location->name;
        $new_site = ($site_id) ? Site::find($site_id) : null;
        $new_location_full = ($new_site) ? "$new_site->address, $new_site->suburb ($new_site->name)" : $other;
        $new_location = ($new_site) ? "$new_site->suburb ($new_site->name)" : $other;

        // Determine if user is already transferring other items from & to same locations
        $location_code = ($site_id) ? "$item->location_id:site:$site_id" : "$item->location_id:other:$other";
        $location = EquipmentLocation::where('notes', "$location_code:$assign")->first();
        if ($location) {
            //
            // Append new item to current user ToDoo
            //
            list ($crap, $todo_id) = explode(':', $location->other);

            // Check if location also has existing item to add qty to.
            $existing = EquipmentLocationItem::where('location_id', $location->id)->where('equipment_id', $item->equipment_id)->first();
            if ($existing) {
                $existing->qty = $existing->qty + $qty;
                $existing->save();
            } else
                $location->items()->save(new EquipmentLocationItem(['location_id' => $location->id, 'equipment_id' => $item->equipment_id, 'qty' => $qty]));
            $log = new EquipmentLog(['equipment_id' => $item->equipment_id, 'qty' => $qty, 'action' => 'X', 'notes' => "Task updated to transfer $qty items from $old_location => $new_location"]);
            $log->save();
        } else {
            //
            // Create temporary location for transfer and new ToDoo for user
            //
            $newLocation = new EquipmentLocation();
            $newLocation->save();
            $newLocation->items()->save(new EquipmentLocationItem(['location_id' => $newLocation->id, 'equipment_id' => $item->equipment_id, 'qty' => $qty]));

            // Create ToDoo and assign to user
            $todo_request = [
                'type'       => 'equipment',
                'type_id'    => $newLocation->id,
                'name'       => "Equipment transfer - $old_location => $new_location",
                'info'       => "Please transfer equipment from the locations below.\nFrom: $old_location_full\nTo: $new_location_full",
                'due_at'     => nextWorkDate(Carbon::today(), '+', 2)->toDateTimeString(),
                'company_id' => $item->company_id,
            ];
            $todo = Todo::create($todo_request);
            $todo->assignUsers($assign);
            $log = new EquipmentLog(['equipment_id' => $item->equipment_id, 'qty' => $qty, 'action' => 'X', 'notes' => "Task created to transfer $qty items from $old_location => $new_location"]);
            $log->save();

            // Update temporary transfer location with details of ToDoo request for tracking
            $newLocation->other = "Transfer in progress:$todo->id";
            $newLocation->notes = "$location_code:$assign";
            $newLocation->save();
        }

        // Subtract items
        $this->subtractItems($item, $qty);
    }

    /**
     * Cancel transfer assign to user
     *
     */
    public function cancelTransfer($id)
    {
        $location = EquipmentLocation::findOrFail($id);

        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('edit.equipment', $location))
            return view('errors/404');

        list($crap, $todo_id) = explode(':', $location->other);
        list($orig_location_id, $type, $details, $user_id) = explode(':', $location->notes);

        $site_id = ($type == 'site') ? $details : null;
        $other = ($type == 'other') ? $details : null;
        $orig_location = EquipmentLocation::findOrFail($orig_location_id);
        $old_site = ($orig_location->site_id) ? Site::find($orig_location->site_id) : null;
        $orig_location_name = ($old_site) ? "$old_site->suburb ($old_site->name)" : $orig_location->name;
        $new_site = ($site_id) ? Site::find($site_id) : null;
        $new_location = ($new_site) ? "$new_site->suburb ($new_site->name)" : $other;

        foreach ($location->items as $item)
            $this->performTransfer($item, $item->qty, $orig_location->site_id, $orig_location->other);

        $log = new EquipmentLog(['equipment_id' => $item->equipment_id, 'qty' => null, 'action' => 'X', 'notes' => "Task cancelled to transfer items from $orig_location_name => $new_location"]);
        $log->save();

        // Delete ToDoo + Transfer Location
        $todo = Todo::find($todo_id);
        $todo->delete();
        $location->delete();
        Toastr::success("Transfer cancelled");

        return redirect("/equipment");
    }

    /**
     * Subtract X items from location
     */
    public function subtractItems($item, $qty)
    {
        // Subtract items from current location
        $new_qty = $item->qty - $qty;
        if ($new_qty) {
            $item->qty = $item->qty - $qty;
            $item->save();
        } else
            $item->delete();
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
     * Write off the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function writeoffItems()
    {
        //dd(request()->all());

        // Check authorisation and throw 404 if not
        if (!Auth::user()->hasPermission2("del.equipment"))
            return view('errors/404');

        if (request('writeoff')) {
            foreach (request('writeoff') as $lost_id) {
                $lost = EquipmentLost::findOrFail($lost_id);
                $lost->equipment->disposed = $lost->equipment->disposed + $lost->qty;
                $lost->equipment->save();
                $log = new EquipmentLog(['equipment_id' => $lost->equipment_id, 'qty' => $lost->qty, 'action' => 'W', 'notes' => "Write off $lost->qty items from " . $lost->created_at->format('d/m/Y')]);
                $log->save();
                $lost->delete();
            }
        }
        Toastr::error("Items written off");

        return redirect("/equipment/inventory");
    }

    /**
     * Get Allocations + Process datatables ajax request.
     */
    public function getAllocation()
    {
        $items_list = (request('equipment_id')) ? [request('equipment_id')] : EquipmentLocationItem::all()->pluck('equipment_id')->toArray();

        if (request('site_id'))
            $items = EquipmentLocationItem::select([
                'equipment_location_items.id', 'equipment_location_items.location_id', 'equipment_location_items.equipment_id', 'equipment_location_items.qty', 'equipment_location_items.company_id',
                'equipment_location.site_id', 'equipment_location.other', 'equipment_location.status', 'equipment_categories.name AS catname',
                'equipment.name AS itemname', 'equipment.status', 'sites.name AS sitename', 'sites.code', 'sites.suburb'])
                ->join('equipment', 'equipment_location_items.equipment_id', '=', 'equipment.id')
                ->join('equipment_location', 'equipment_location_items.location_id', '=', 'equipment_location.id')
                ->join('equipment_categories', 'equipment_categories.id', '=', 'equipment.category_id')
                ->leftjoin('sites', 'equipment_location.site_id', '=', 'sites.id')
                ->whereIn('equipment_location_items.equipment_id', $items_list)
                ->where('equipment.status', 1)
                ->where('equipment_location.status', 1)
                ->where('equipment_location.site_id', request('site_id'));
        else {
            $items = EquipmentLocationItem::select([
                'equipment_location_items.id', 'equipment_location_items.location_id', 'equipment_location_items.equipment_id', 'equipment_location_items.qty', 'equipment_location_items.company_id',
                'equipment_location.site_id', 'equipment_location.other', 'equipment_location.status', 'equipment_categories.name AS catname',
                'equipment.name AS itemname', 'equipment.status', 'sites.name AS sitename', 'sites.code', 'sites.suburb'])
                ->join('equipment', 'equipment_location_items.equipment_id', '=', 'equipment.id')
                ->join('equipment_location', 'equipment_location_items.location_id', '=', 'equipment_location.id')
                ->join('equipment_categories', 'equipment_categories.id', '=', 'equipment.category_id')
                ->leftjoin('sites', 'equipment_location.site_id', '=', 'sites.id')
                ->where('equipment.status', 1)
                ->where('equipment_location.status', 1)
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
                $action = '';
                if (Auth::user()->allowed2('edit.equipment', $item)) {
                    if ($item->inTransit() && Auth::user()->allowed2('view.todo', $item->inTransit()))
                        $action .= "<a href='/todo/" . $item->inTransit()->id . "' class='btn blue btn-xs btn-outline sbold uppercase margin-bottom'>View Task</a>";
                    elseif (!$item->inTransit())
                        $action .= "<a href='/equipment/$item->id/transfer' class='btn blue btn-xs btn-outline sbold uppercase margin-bottom'>Transfer</a>";
                }

                return $action;
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
        $equipment = Equipment::select([
            'equipment.id', 'equipment.category_id', 'equipment.name', 'equipment.purchased', 'equipment.disposed', 'equipment.status', 'equipment.company_id',
            'equipment_categories.name AS catname'
        ])
            ->join('equipment_categories', 'equipment_categories.id', '=', 'equipment.category_id')
            ->where('equipment.status', 1);

        $dt = Datatables::of($equipment)
            ->editColumn('id', function ($equip) {
                return '<div class="text-center"><a href="/equipment/' . $equip->id . '"><i class="fa fa-search"></i></a></div>';
            })
            ->addColumn('total', function ($equip) {
                $str = $equip->total;
                if ($equip->total_excess > 0)
                    $str = "<span class='label label-warning'>$equip->total</span>";
                if ($equip->total_excess < 0)
                    $str = "<span class='label label-danger'>$equip->total</span>";

                return $str;
            })
            ->addColumn('lost', function ($equip) {
                return ($equip->total_lost) ? $equip->total_lost : '-';
            })
            ->addColumn('action', function ($equip) {
                return (Auth::user()->hasPermission2('add.equipment')) ? '<a href="/equipment/' . $equip->id . '/edit" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom"><i class="fa fa-pencil"></i> Edit</a>' : '';
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
