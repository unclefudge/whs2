<?php

namespace App\Jobs;

use DB;
use PDF;
use Log;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Models\Company\Company;
use App\Models\Site\Site;
use App\Models\Site\SiteQa;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SiteQaPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $site_id, $data, $output_file;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($site_id, $data, $output_file)
    {
        $this->site_id = $site_id;
        $this->data = $data;
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

        /*
        $data = [];
        $users = [];
        $companies = [];
        $site_qa = SiteQa::where('site_id', $site->id)->where('status', '<>', '-1')->where('company_id', '3')->get();
        foreach ($site_qa as $qa) {
            $obj_qa = (object) [];
            $obj_qa->id = $qa->id;
            $obj_qa->name = $qa->name;
            $obj_qa->status = $qa->status;
            // Signed By Super
            $obj_qa->super_sign_by = '';
            if ($qa->supervisor_sign_by) {
                if (!isset($users[$qa->supervisor_sign_by]))
                    $users[$qa->supervisor_sign_by] = User::find($qa->supervisor_sign_by);
                $obj_qa->super_sign_by = $users[$qa->supervisor_sign_by]->fullname;
            }
            $obj_qa->super_sign_at = ($qa->supervisor_sign_by) ? $qa->supervisor_sign_at->format('d/m/Y') : '';
            // Signed By Manager
            $obj_qa->manager_sign_by = '';
            if ($qa->manager_sign_by) {
                if (!isset($users[$qa->manager_sign_by]))
                    $users[$qa->manager_sign_by] = User::find($qa->manager_sign_by);
                $obj_qa->manager_sign_by = $users[$qa->manager_sign_by]->fullname;
            }
            $obj_qa->manager_sign_at = ($qa->manager_sign_by) ? $qa->manager_sign_at->format('d/m/Y') : '';
            $obj_qa->items = [];
            $obj_qa->actions = [];

            // Items
            foreach ($qa->items as $item) {
                $obj_qa->items[$item->order]['id'] = $item->id;
                $obj_qa->items[$item->order]['name'] = $item->name;
                $obj_qa->items[$item->order]['status'] = $item->status;
                $obj_qa->items[$item->order]['done_by'] = '';
                $obj_qa->items[$item->order]['sign_by'] = '';
                $obj_qa->items[$item->order]['sign_at'] = '';

                // Item Completed + Signed Off
                if ($item->status == '1') {
                    // Get User Signed
                    if (!isset($users[$item->sign_by]))
                        $users[$item->sign_by] = User::find($item->sign_by);
                    $user_signed = $users[$item->sign_by];
                    // Get Company
                    $company = $user_signed->company;
                    if ($item->done_by) {
                        if (!isset($companies[$item->done_by]))
                            $companies[$item->done_by] = Company::find($item->done_by);
                        $company = $companies[$item->done_by];
                    }
                    $obj_qa->items[$item->order]['done_by'] = $company->name_alias . " (lic. $company->licence_no)";
                    $obj_qa->items[$item->order]['sign_by'] = $user_signed->fullname;
                    $obj_qa->items[$item->order]['sign_at'] = $item->sign_at->format('d/m/Y');
                }
            }

            // Action
            foreach ($qa->actions as $action) {
                if (!preg_match('/^Moved report to/', $action->action)) {
                    $obj_qa->actions[$action->id]['action'] = $action->action;
                    if (!isset($users[$action->created_by]))
                        $users[$action->created_by] = User::find($action->created_by);
                    $obj_qa->actions[$action->id]['created_by'] = $users[$action->created_by]->fullname;
                    $obj_qa->actions[$action->id]['created_at'] = $action->created_at->format('d/m/Y');
                }
            }
            $data[] = $obj_qa;
        }*/

        $pdf = PDF::loadView('pdf/site-qa', compact('site', 'data'));
        $pdf->setPaper('a4');
        $pdf->save($this->output_file);
    }
}
