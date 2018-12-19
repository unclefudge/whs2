<?php

namespace App\Jobs;

use DB;
use PDF;
use Log;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Models\Misc\Equipment\Equipment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class EquipmentSitePdf implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $locations, $output_file;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($locations, $output_file)
    {
        $this->locations = $locations;
        $this->output_file = $output_file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $locations = $this->locations;

        $pdf = PDF::loadView('pdf/equipment-site', compact('locations'));
        $pdf->setPaper('a4');
        $pdf->save($this->output_file);
    }
}
