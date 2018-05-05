<?php

namespace App\Mail\Company;

use App\Models\Company\CompanyDocPeriodTrade;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CompanyPeriodTradeRejected extends Mailable implements ShouldQueue {

    use Queueable, SerializesModels;

    public $ptc;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(CompanyDocPeriodTrade $ptc)
    {
        $this->ptc = $ptc;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $file_path = public_path($this->ptc->attachment_url);
        if ($this->ptc->attachment && file_exists($file_path))
            return $this->markdown('emails/company/ptc-rejected')->subject('SafeWorksite - Contract Not Approved')->attach($file_path);

        return $this->markdown('emails/company/ptc-rejected')->subject('SafeWorksite - Contract Not Approved');
    }
}
