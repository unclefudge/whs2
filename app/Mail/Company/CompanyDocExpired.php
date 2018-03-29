<?php

namespace App\Mail\Company;

use App\Models\Company\CompanyDoc;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CompanyDocExpired extends Mailable {

    use Queueable, SerializesModels;

    public $doc;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(CompanyDoc $doc)
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
        $expired = ($this->doc->expiry->lt(Carbon::today())) ? "has Expired " . $this->doc->expiry->format('d/m/Y') : "due to expire " . $this->doc->expiry->format('d/m/Y');
        $file_path = public_path($this->doc->attachment_url);
        if ($this->doc->attachment && file_exists($file_path))
            return $this->markdown('emails/company/doc-expired')->subject("SafeWorksite - Document $expired")->attach($file_path);

        return $this->markdown('emails/company/doc-expired')->subject("SafeWorksite - Document $expired");
    }
}
