<?php

namespace App\Mail\Safety;

use App\Models\Safety\ToolboxTalk;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ToolboxTalkModifiedTemplate extends Mailable implements ShouldQueue {

    use Queueable, SerializesModels;

    public $talk;
    public $diffs;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ToolboxTalk $talk, $diffs)
    {
        $this->talk = $talk;
        $this->diffs = $diffs;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails/safety/toolbox-modified-template')->subject('SafeWorksite - Toolbox Talk');
    }
}
