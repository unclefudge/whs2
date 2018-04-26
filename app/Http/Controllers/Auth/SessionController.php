<?php

namespace App\Http\Controllers\Auth;

use DB;
use Auth;
use Session;
use App\User;
use App\Models\Company\Company;
use App\Models\Site\Site;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SessionController extends Controller {

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'destroy']);
    }

    /**
     * Create New Session - show login form
     */
    protected function create()
    {
        $worksite = '';
        Auth::logout();
        Session::forget('siteID');

        return view('auth/login', compact('worksite'));
    }

    /**
     * Store new session - login user
     */
    protected function store()
    {
        $email = preg_match('/@/', request('username')) ? true : false;

        $credentials = ($email) ? ['email' => request('username'), 'password' => request('password')] :
            ['username' => request('username'), 'password' => request('password')];

        if (auth()->attempt($credentials)) {
            // Inactive user
            if (!Auth::user()->status) {
                Auth::logout();

                return back()->withErrors(['message' => 'These credentials do not match our records.']);
            }

            // Record last_login but disable timestamps to preserve last time record was updated.
            Auth::user()->last_login = Carbon::now();
            Auth::user()->updated_by = Auth::user()->updated_by;
            Auth::user()->timestamps = false;
            Auth::user()->save();

            // Log Supervisors
            if (Auth::user()->isSupervisor())
                File::append(public_path('filebank/log/users/supers_login.txt'), Carbon::now()->format('d/m/Y H:i:s') . ' ' . Auth::user()->fullname . ' (' . Auth::user()->username . ")\n");

            // Display Site Specific Alerts
            /*
            if (Session::has('siteID')) {
                $site = Site::where('code', Session::get('siteID'))->first();
                $today = Carbon::today();
                $notifys = Notify::where('type', 'site')->where('type_id', $site->id)
                    ->where('from', '<=', $today)->where('to', '>=', $today)->get();

                //Toastr::success($site->id);
                foreach ($notifys as $notify) {
                    if ($notify->action == 'many' || !$notify->isOpenedBy($user))
                        alert()->message($notify->info, $notify->name)->persistent('Ok');
                    if (!$notify->isOpenedBy($user))
                        $notify->markOpenedBy($user);
                }
            }*/

            // Display User Specific Alerts
            foreach (Auth::user()->notify() as $notify) {
                //$mesg = ($notify->isOpenedBy($user)) ? '[1]' : '[0]';
                $mesg = $notify->info; // . $mesg;
                alert()->message($mesg, $notify->name)->persistent('Ok');
                if (!$notify->isOpenedBy(Auth::user()))
                    $notify->markOpenedBy(Auth::user());
            }

            if (Auth::user()->password_reset)
                return redirect('/user/' . Auth::user()->id . '/edit');

            return redirect()->intended('home');
            //return redirect('/dashboard');
        }

        return back()->withErrors(['message' => 'These credentials do not match our records.']);
    }

    /**
     * Destroy session - logout user
     */
    protected function destroy()
    {
        // Logout user + clear session
        Auth::logout();
        Session::flush();

        return redirect('/');
    }
}
