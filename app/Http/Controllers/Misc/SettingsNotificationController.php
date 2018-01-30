<?php

namespace App\Http\Controllers\Misc;


use Illuminate\Http\Request;

use DB;
use Mail;
use App\User;
use App\Models\Company\Company;
use App\Models\Misc\SettingsNotification;
use App\Http\Requests;
use App\Http\Requests\Misc\RoleRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use nilsenj\Toastr\Facades\Toastr;
use Carbon\Carbon;


class SettingsNotificationController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //if (!Auth::user()->security)
        //    return view('errors/404');
        return view('manage/settings/notifications/edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        for ($i = 1; $i < 8; $i ++) {
            $users = $request->get("type$i");
            $this->syncUsers($id, $i, $users);
        }

        Toastr::success('Saved notifications');

        return view('manage/settings/notifications/edit');
    }

    public function show()
    {
        //
    }

    /**
     * Delete the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //
    }


    /**
     * Sync Users
     */
    public function syncUsers($company_id, $type, $users)
    {
        // Delete any lookup records
        $deleted_records = SettingsNotification::where('company_id', $company_id)->where('type', $type)->delete();

        // Create new lookup records
        if ($users) {
            foreach ($users as $user_id) {
                $newNotification = SettingsNotification::create(['user_id' => $user_id, 'type' => $type, 'company_id' => $company_id]);
            }
        }

    }
}
