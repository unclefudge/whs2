<?php

namespace App\Mail\Site;

use App\Models\Site\SiteHazard;
use App\Models\Misc\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SiteHazardCreated extends Mailable {

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
        $file_path = public_path($this->hazard->attachment_url);
        if ($this->hazard->attachment && file_exists($file_path))
            return $this->markdown('emails/site/hazard-created')->subject('SafeWorksite - Hazard Notification')->attach($file_path);

        return $this->markdown('emails/site/hazard-created')->subject('SafeWorksite - Hazard Notification');
    }
}
