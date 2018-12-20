<?php

namespace App\Jobs;

use DB;
use PDF;
use Log;
use Illuminate\Support\Facades\Auth;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class EquipmentTransactionsPdf implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transactions, $from, $to,$output_file;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transactions, $from, $to, $output_file)
    {
        $this->transactions = $transactions;
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
        $transactions = $this->transactions;
        $from = $this->from;
        $to = $this->to;

        $pdf = PDF::loadView('pdf/equipment-transactions', compact('transactions', 'from', 'to'));
        $pdf->setPaper('a4');
        $pdf->save($this->output_file);
    }
}
