<?php

namespace App\Mail\Site;

use App\Models\Site\SiteMaintenance;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SiteMaintenanceAssigned extends Mailable implements ShouldQueue {

    use Queueable, SerializesModels;

    public $main;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SiteMaintenance $main)
    {
        $this->main = $main;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails/site/maintenance-assigned')->subject('SafeWorksite - Maintenance Request Notification');
    }
}
