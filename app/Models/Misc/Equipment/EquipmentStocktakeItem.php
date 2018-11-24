<?php

namespace App\Models\Misc\Equipment;

use URL;
use Mail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EquipmentStocktakeItem extends Model {

    protected $table = 'equipment_stocktake_items';
    protected $fillable = ['stocktake_id', 'equipment_id', 'qty_expect', 'qty_actual', 'passed', 'company_id', 'created_by', 'created_at', 'updated_at', 'updated_by'];

    /**
     * A EquipmentStocktakeItem belongs to a Equipment Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stocktake()
    {
        return $this->belongsTo('App\Models\Misc\Equipment\EquipmentStocktake', 'stocktake_id');
    }

    /**
     * A EquipmentStocktakeItem belongs to a Equipment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function equipment()
    {
        return $this->belongsTo('App\Models\Misc\Equipment\Equipment', 'equipment_id');
    }

    /**
     * A EquipmentStocktakeItem belongs to a User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }

    /**
     * Get the Item Total  (getter)
     */
    public function getItemNameAttribute()
    {
        return $this->equipment->name;
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
                $table->company_id = Auth::user()->company_id;
            });

            // create a event to happen on updating
            static::updating(function ($table) {
                $table->updated_by = Auth::user()->id;
            });
        }
    }
}