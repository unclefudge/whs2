<?php

namespace App\Models\Misc\Equipment;

use URL;
use Mail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EquipmentLog extends Model {

    protected $table = 'equipment_log';
    protected $fillable = ['equipment_id', 'qty', 'action', 'notes', 'company_id', 'created_by', 'created_at', 'updated_at', 'updated_by'];

    /**
     * A EquipmentTransaction belongs to a Equipment Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item()
    {
        return $this->belongsTo('App\Models\Misc\Equipment\Equipment', 'equipment_id');
    }

    /**
     * A EquipmentTransaction belongs to a User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'updated_by');
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
                $table->company_id = 3; //Auth::user()->company_id;
            });

            // create a event to happen on updating
            static::updating(function ($table) {
                $table->updated_by = Auth::user()->id;
            });
        }
    }
}