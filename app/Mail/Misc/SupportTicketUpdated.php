<?php

namespace App\Mail\Misc;

use App\Models\Support\SupportTicket;
use App\Models\Support\SupportTicketAction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SupportTicketUpdated extends Mailable implements ShouldQueue {

    use Queueable, SerializesModels;

    public $ticket;
    public $action;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SupportTicket $ticket, SupportTicketAction $action)
    {
        $this->ticket = $ticket;
        $this->action = $action;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $file_path = public_path($this->action->attachment_url);
        if ($this->action->attachment && file_exists($file_path))
            return $this->markdown('emails/misc/support-ticket-updated')->subject('SafeWorksite - Support Ticket Updated')->attach($file_path);

        return $this->markdown('emails/misc/support-ticket-updated')->subject('SafeWorksite - Support Ticket Updated');
    }
}
