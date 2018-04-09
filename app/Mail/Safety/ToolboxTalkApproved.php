<?php

namespace App\Mail\Safety;

use App\Models\Safety\ToolboxTalk;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ToolboxTalkApproved extends Mailable implements ShouldQueue {

    use Queueable, SerializesModels;

    public $talk;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ToolboxTalk $talk)
    {
        $this->talk = $talk;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails/safety/toolbox-approved')->subject('SafeWorksite - Toolbox Talk Approved');
    }
}
