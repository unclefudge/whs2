<?php

namespace App\Http\Controllers\Auth;

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
        /*if (Session::has('siteID')) {
            $site_id = Session::get('siteID');
            $worksite = Site::where(['code' => $site_id])->first();
            if (!$worksite)
                Session::forget('siteID');
        }*/

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
            Auth::user()->timestamps = false;
            Auth::user()->save();

            if (Auth::user()->password_reset)
                return redirect('/user/' . Auth::user()->id . '/edit');

            return redirect('/dashboard');
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
