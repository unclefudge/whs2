<?php

namespace App\Mail\Site;

use App\Models\Site\SiteAccident;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SiteAccidentCreated extends Mailable implements ShouldQueue {

    use Queueable, SerializesModels;

    public $accident;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SiteAccident $accident)
    {
        $this->accident = $accident;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails/site/accident-created')->subject('SafeWorksite - Accident Notification');
    }
}
