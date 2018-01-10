<?php

namespace App\Mail\User;

use App\User;
use App\Models\Company\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserWelcome extends Mailable
{
    use Queueable, SerializesModels;

    public $user, $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails/user/welcome')->subject('Welcome to SafeWorksite');
    }
}
