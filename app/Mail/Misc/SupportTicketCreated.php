<?php

namespace App\Mail\Misc;

use App\User;
use App\Models\Support\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SupportTicketCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $ticket;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SupportTicket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->ticket->attachment)
        return $this->markdown('emails/misc/support-ticket-created')
            ->subject('SafeWorksite - New Support Ticket')
            ->attach(public_path('filebank/support/ticket/'.$this->ticket->attachment));
        else
            return $this->markdown('emails/misc/support-ticket-created')->subject('SafeWorksite - New Support Ticket');
    }
}
