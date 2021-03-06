<?php

namespace App\Models\Misc\Equipment;

use App\Models\Comms\Todo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EquipmentLocationItem extends Model {

    protected $table = 'equipment_location_items';
    protected $fillable = ['location_id', 'equipment_id', 'qty', 'company_id', 'created_by', 'created_at', 'updated_at', 'updated_by'];

    /**
     * A EquipmentLocationItem belongs to a Equipment Location
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo('App\Models\Misc\Equipment\EquipmentLocation', 'location_id');
    }

    /**
     * A EquipmentLocationItem belongs to a Equipment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function equipment()
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
     * A EquipmentTransaction MAY be currently transferred
     *
     */
    public function inTransit()
    {
        return Todo::where('type', 'equipment')->where('type_id', $this->location_id)->first();
    }

    /**
     * Get the Item Total  (getter)
     */
    public function getItemNameAttribute()
    {
        return $this->equipment->name;
    }

    /**
     * Get the Item Total  (getter)
     */
    public function getItemCategoryNameAttribute()
    {
        return $this->equipment->category->name;
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