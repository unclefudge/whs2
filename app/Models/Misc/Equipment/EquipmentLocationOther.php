<?php

namespace App\Models\Misc\Equipment;

use App\User;
use App\Models\Comms\Todo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EquipmentLocationOther extends Model {

    protected $table = 'equipment_location_other';
    protected $fillable = ['type', 'name', 'status', 'notes', 'company_id', 'created_by', 'created_at', 'updated_at', 'updated_by'];


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