<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /*
     * OVERRRIDE DEFAULT
     */

    /*
    public function sendResetLinkEmail(Request $request)
    {
        //dd(request()->all());

        $this->validate($request, ['email' => 'required|email']);

        $response = Password::sendResetLink($request->only('email'), function (Message $message) {
            $message->subject($this->getEmailSubject());
        });

        //echo $this->getEmailSubject();
        //echo url(config('app.url').route('password.reset', $this->token, false));
        echo url(config('app.url').route('password.reset', '$2y$10$6y0l8hc5dyarS2JMogPlyOnOl1H/GFMRLCHKADesblIxtaY08nbKa', false));

        dd($response);
        switch ($response) {
            case Password::RESET_LINK_SENT:
                return redirect()->back()->with('status', trans($response));
            case Password::INVALID_USER:
                return redirect()->back()->withErrors(['email' => trans($response)]);
        }


        //return (new MailMessage)
        //    ->line('You are receiving this email because we received a password reset request for your account.')
        //    ->action('Reset Password', url(config('app.url').route('password.reset', $this->token, false)))
        //    ->line('If you did not request a password reset, no further action is required.');

    }*/
}
