<?php

namespace App\Http\Controllers\Auth;

use DB;
use Auth;
Use Mail;
use Session;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use nilsenj\Toastr\Facades\Toastr;

class PasswordResetController extends Controller {

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('guest');
    }


    /**
     * Forgot Password Form
     */
    protected function forgotForm()
    {
        return view('auth/forgot-password');
    }

    /**
     * Reset Password Form
     */
    protected function resetForm($token)
    {
        $token = request()->query('token');
        return view('auth/reset-password', compact('token'));
    }

    /**
     * Reset Email
     */
    protected function resetEmail()
    {
        $user = User::where('email', request('email'))->first();

        // Email Reset Link
        if ($user && $user->status && validEmail($user->email)) {

            // Delete existing token if exists
            $exists = DB::table('password_resets')->where(['email' => $user->email])->delete();

            // Create token
            $token = bcrypt($user->email);
            DB::table('password_resets')->insert(['email' => $user->email, 'token' => $token, 'created_at' => Carbon::now()->toDateTimeString()]);

            // Send out Password Reset Email to user
            Mail::to($user)->send(new \App\Mail\Misc\PasswordReset($user, $token));

            return redirect()->back()->with('message', 'Password reset link has been emailed');
        }

        return redirect()->back();
    }

    /**
     * Reset Password
     */
    protected function reset()
    {
        $this->validate(request(), [
            'email'              => 'required|email',
            'password'           => 'required|confirmed|min:3',
        ]);

        $user = User::where('email', request('email'))->first();

        if ($user && $user->status) {
            $match = DB::table('password_resets')->where(['email' => $user->email, 'token' => request('token')])->first();

            // Reset Password
            if ($match) {
                // Sign in User
                auth()->login($user);

                // Update User
                $user_request = request()->all();
                $user_request['password'] = bcrypt($user_request['password']);
                $user->update($user_request);

                // Delete existing token
                DB::table('password_resets')->where(['email' => $user->email])->delete();

                Toastr::success("Reset password");

                return redirect("/");
            }
        }


        return redirect()->back()->withErrors(['email' => 'Invalid email']);
    }
}
