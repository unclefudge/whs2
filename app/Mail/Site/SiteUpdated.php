<?php

namespace App\Mail\Site;

use App\Models\Site\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SiteUpdated extends Mailable implements ShouldQueue {

    use Queueable, SerializesModels;

    public $site;
    public $action;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Site $site, $action)
    {
        $this->site = $site;
        $this->action = $action;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->action == 'new')
            return $this->markdown('emails/site/site-updated')->subject('SafeWorksite - New Site');

        return $this->markdown('emails/site/site-updated')->subject('SafeWorksite - Site Status Updated');
    }
}
