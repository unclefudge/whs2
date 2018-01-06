<?php

namespace App\Models\Site\Planner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SiteComplianceReason extends Model {

    protected $table = 'site_compliance_reasons';
    protected $fillable = ['name', 'status', 'company_id'];

    /**
     * A SiteComplianceReason belongs to a company
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    /*public function company()
    {
        return $this->belongsTo('App\Company', 'company_id');
    }*/
}