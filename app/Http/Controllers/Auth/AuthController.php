<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Session;
use Auth;
use App\User;
use App\Models\Site\Site;
use Validator;
use App\Http\Controllers\Controller;
//use Illuminate\Foundation\Auth\ThrottlesLogins;
//use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    //use AuthenticatesAndRegistersUsers, ThrottlesLogins;
    use AuthenticatesUsers;

    protected $redirectPath = '/dashboard';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|min:3|max:50|unique:users|exists:users,email,status,1',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:3',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }


    /**
     * Get the needed authorization credentials from the request.
     *
     *  OVERRIDES the default from Illuminate\Foundation\Auth\AuthenticatesUsers
     *
     * @param Request $request
     * @return array
     */
    protected function getCredentials(Request $request)
    {
        // Allow users to login using username or email
        $field = filter_var($request->input('username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $request->merge([$field => $request->input('username')]);
        $crendentials = $request->only($field, 'password');

        // Only allow Active users ie. status '1'
        $crendentials['status']=1;

        return $crendentials;
    }

    /**
     * Show the application login form.
     *
     * OVERRIDES the default from Illuminate\Foundation\Auth\AuthenticatesUsers
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {
        $worksite = '';
        if (Session::has('siteID')) {
            $site_id = Session::get('siteID');
            $worksite = Site::where([ 'code' => $site_id])->first();
            if (!$worksite)
                Session::forget('siteID');
        }

        return view('auth.login', compact('worksite'));
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {

        //dd($request->all());
        // Attempt ot authenticate user

        if (Auth::attempt(['username' => $request->get('username'), 'password' => $request->get('password')]) ||
            Auth::attempt(['email' => $request->get('username'), 'password' => $request->get('password')])) {
            return redirect('/dashboard');
        }

        return back()->withErrors(['username' => 'These credentials do not match our records.']);
    }

    /*
    public function getLoginSite()
    {
        if (view()->exists('auth.authenticate')) {
            return view('auth.authenticate');
        }

        $worksite = '';
        if (Session::has('siteID')) {
            $site_id = Session::get('siteID');
            $worksite = Site::where([ 'code' => $site_id])->first();
            if (!$worksite)
                Session::forget('siteID');
        }

        return view('auth.login', compact('worksite'));
    }*/

    /**
     * Log the user out of the application.
     *
     * OVERRIDES the default from Illuminate\Foundation\Auth\AuthenticatesUsers
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogout()
    {
        $redirectTo = '/';
        /*if (Session::has('siteID')) {
            $redirectTo = '/site/login/' . Session::get('siteID');
        }*/
        Auth::logout();
        Session::flush();
        //Session::forget('siteID');

        return redirect($redirectTo);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * OVERRIDES the default from Illuminate\Foundation\Auth\AuthenticatesUsers
     *
     * @return string
     */
    public function loginUsername()
    {
        return 'username';
    }
}
