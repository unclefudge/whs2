<?php

namespace App\Mail\Site;

use App\Models\Site\SiteHazard;
use App\Models\Misc\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SiteHazardAction extends Mailable implements ShouldQueue {

    use Queueable, SerializesModels;

    public $hazard;
    public $action;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SiteHazard $hazard, Action $action)
    {
        $this->hazard = $hazard;
        $this->action = $action;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails/site/hazard-action')->subject('SafeWorksite - Hazard Notification');
    }
}
