<?php

namespace App\Mail\User;

use App\User;
use App\Models\Company\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $user, $created_by;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, User $created_by)
    {
        $this->user = $user;
        $this->created_by = $created_by;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails/user/created')->subject('SafeWorksite - New user');
    }
}
