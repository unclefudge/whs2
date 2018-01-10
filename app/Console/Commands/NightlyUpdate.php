<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class NightlyUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:nightly-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nightly update of roster + non-compliance';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info('Nightly update of roster + non-compliance');
        //\App\Models\Site\Planner\SiteRoster::nightlyUpdate();
        \App\Http\Controllers\Misc\CronController::nightly();
    }
}
