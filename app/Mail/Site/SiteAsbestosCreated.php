<?php

namespace App\Mail\Site;

use App\Models\Site\SiteAsbestos;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SiteAsbestosCreated extends Mailable implements ShouldQueue {

    use Queueable, SerializesModels;

    public $asbestos;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SiteAsbestos $asbestos)
    {
        $this->asbestos = $asbestos;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails/site/asbestos-created')->subject('SafeWorksite - Asbestos Notification');
    }
}
