<?php

namespace App\Models\Safety;

use App\User;
use App\Models\Company\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class WmsControl extends Model {

    protected $table = 'wms_controls';
    protected $fillable = [
        'step_id', 'name', 'order', 'res_principle', 'res_company', 'res_worker', 'master', 'master_id',
        'created_by', 'updated_by', 'created_at', 'updated_at'];

    /**
     * A Control belongs to a Step
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function step()
    {
        return $this->belongsTo('App\Models\Safety\WmsStep', 'step_id');
    }

    /**
     * Get the ResponsibleName (getter)
     */
    public function getResponsibleNameAttribute()
    {
        $string = '';
        if ($this->attributes['res_principle']) {
            $string = "Principal Contractor"; //$this->step->document->principleName;
        }
        if ($this->attributes['res_company']) {
            ($string) ? $string .= ' & ' : '';
            $string .= ($this->step->document->for_company_id) ? $this->step->document->company->name  : "Company";
        }
        if ($this->attributes['res_worker']) {
            ($string) ? $string .= ' & ' : '';
            $string .= 'Worker'; //$this->step->document->company->name;
        }

        return $string;
    }

    /**
     * Get the owner of record   (getter)
     *
     * @return string;
     */
    public function getOwnedByAttribute()
    {
        return $this->step->owned_by;
    }

    /**
     * Display records last update_by + date
     *
     * @return string
     */
    public function displayUpdatedBy()
    {
        $user = User::findOrFail($this->updated_by);

        return '<span style="font-weight: 400">Last modified: </span>' . $this->updated_at->diffForHumans() . ' &nbsp; ' .
        '<span style="font-weight: 400">By:</span> ' . $user->fullname;
    }

    /**
     * The "booting" method of the model.
     *
     * Overrides parent function
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        if (Auth::check()) {
            // create a event to happen on creating
            static::creating(function ($table) {
                $table->created_by = Auth::user()->id;
                $table->updated_by = Auth::user()->id;
            });

            // create a event to happen on updating
            static::updating(function ($table) {
                $table->updated_by = Auth::user()->id;
            });
        }
    }

}

