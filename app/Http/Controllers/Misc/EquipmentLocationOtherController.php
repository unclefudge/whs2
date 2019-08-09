<?php

namespace App\Http\Controllers\Misc;

use Illuminate\Http\Request;
use Validator;

use DB;
use Session;
use App\User;
use App\Models\Misc\Equipment\Equipment;
use App\Models\Misc\Equipment\EquipmentLocation;
use App\Models\Misc\Equipment\EquipmentLocationOther;
use App\Models\Misc\Equipment\EquipmentLocationItem;
use App\Models\Misc\Equipment\EquipmentLost;
use App\Models\Misc\Equipment\EquipmentLog;
use App\Models\Misc\Equipment\EquipmentCategory;
use App\Models\Comms\Todo;
use App\Models\Site\Site;
use Intervention\Image\Facades\Image;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use nilsenj\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Alert;

class EquipmentLocationOtherController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.equipment'))
            return view('errors/404');

        return view('misc/equipment/other/list');
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
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        // Check authorisation and throw 404 if not
        if (!Auth::user()->allowed2('add.equipment') && Auth::user()->company_id == 3)
            return view('errors/404');

        request()->validate(['name' => 'required', 'subcategory_id' => 'required_if:category_id,3'], ['subcategory_id.required_if' => 'The sub-category field is required.']); // Validate

        // Create Item
        $equip_request = request()->all();
        if (request('category_id') == 3)
            $equip_request['category_id'] = request('subcategory_id');

        $equip = Equipment::create($equip_request);
        $qty = request('purchase_qty');

        // Handle attached Photo or Video
        if (request()->hasFile('media')) {
            $file = request()->file('media');
            $path = "filebank/equipment/";
            $name = 'e' . $equip->id . '.' . strtolower($file->getClientOriginalExtension());
            $path_name = $path . '/' . $name;
            $file->move($path, $name);

            // resize the image to a width of 1024 and constrain aspect ratio (auto height)
            if (exif_imagetype($path_name)) {
                Image::make(url($path_name))
                    ->resize(1024, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->save($path_name);
            } else
                Toastr::error("Bad image");

            $equip->attachment = $name;
            $equip->save();
        }

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

        request()->validate(['name' => 'required', 'subcategory_id' => 'required_if:category_id,3'], ['subcategory_id.required_if' => 'The sub-category field is required.']); // Validate

        // Update Equipment
        $equip_request = request()->all();
        if (request('category_id') == 3)
            $equip_request['category_id'] = request('subcategory_id');

        $equip->update($equip_request);

        $qty = request('purchase_qty');

        // Handle attached Photo or Video
        if (request()->hasFile('media')) {
            $file = request()->file('media');
            $path = "filebank/equipment/";
            $name = 'e' . $equip->id . '.' . strtolower($file->getClientOriginalExtension());
            $path_name = $path . '/' . $name;
            $file->move($path, $name);

            // resize the image to a width of 1024 and constrain aspect ratio (auto height)
            if (exif_imagetype($path_name)) {
                Image::make(url($path_name))
                    ->resize(1024, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->save($path_name);
            } else
                Toastr::error("Bad image");

            $equip->attachment = $name;
            $equip->save();
        }

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
     * Get Equipment Inventory + Process datatables ajax request.
     */
    public function getOther()
    {
        $other = EquipmentLocationOther::where('status', 1)->get();
        $dt = Datatables::of($other)
            ->editColumn('id', function ($other) {
                return '<div class="text-center"><a href="/equipment/' . $other->id . '"><i class="fa fa-search"></i></a></div>';
            })
            ->addColumn('action', function ($other) {
                return (Auth::user()->hasPermission2('add.equipment')) ? '<a href="/equipment/' . $other->id . '/edit" class="btn blue btn-xs btn-outline sbold uppercase margin-bottom"><i class="fa fa-pencil"></i> Edit</a>' : '';
            })
            ->rawColumns(['id', 'total', 'action'])
            ->make(true);

        return $dt;
    }
}
