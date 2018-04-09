<?php

namespace App\Mail\Site;

use App\Models\Site\SiteAsbestos;
use App\Models\Misc\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SiteAsbestosAction extends Mailable implements ShouldQueue {

    use Queueable, SerializesModels;

    public $asbestos;
    public $action;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SiteAsbestos $asbestos, Action $action)
    {
        $this->asbestos = $asbestos;
        $this->action = $action;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails/site/asbestos-action')->subject('SafeWorksite - Asbestos Notification');
    }
}
