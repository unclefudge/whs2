<?php

namespace App\Mail\User;

use App\User;
use App\Models\User\UserDoc;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserDocRejected extends Mailable implements ShouldQueue
{

    use Queueable, SerializesModels;

    public $doc;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(UserDoc $doc)
    {
        $this->doc = $doc;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $file_path = public_path($this->doc->attachment_url);
        if ($this->doc->attachment && file_exists($file_path))
            return $this->markdown('emails/user/doc-rejected')->subject('SafeWorksite - Document Not Approved')->attach($file_path);

        return $this->markdown('emails/user/doc-rejected')->subject('SafeWorksite - Document Not Approved');
    }
}
