<?php

namespace App\Jobs;

use DB;
use PDF;
use Log;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Models\Site\Site;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SiteAttendancePdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $site_id, $data, $company, $from, $to, $output_file;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $site_id, $company, $from, $to, $output_file)
    {
        $this->data = $data;
        $this->site_id = $site_id;
        $this->company = $company;
        $this->from = $from;
        $this->to = $to;
        $this->output_file = $output_file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $site = Site::findOrFail($this->site_id);
        $data = $this->data;
        $company = $this->company;
        $from = $this->from;
        $to = $this->to;

        $pdf = PDF::loadView('pdf/site-attendance', compact('data', 'site', 'company', 'from', 'to'))->setPaper('a4', 'landscape')->save($this->output_file);
    }
}
