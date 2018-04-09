<?php

namespace App\Mail\Comms;

use App\Models\Comms\Todo;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TodoCreated extends Mailable implements ShouldQueue {

    use Queueable, SerializesModels;

    public $todo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Todo $todo)
    {
        $this->todo = $todo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $overdue = '';
        if ($this->todo->due_at && $this->todo->due_at->lt(Carbon::today()))
            $overdue = ' - OVERDUE';

        return $this->markdown('emails/comms/todo-created')->subject("SafeWorksite - Todo Notification $overdue");
    }
}
