<?php

namespace App\Models\Misc\Equipment;

use DB;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EquipmentStocktake extends Model {

    protected $table = 'equipment_stocktake';
    protected $fillable = ['location_id', 'passed', 'notes', 'company_id', 'created_by', 'created_at', 'updated_at', 'updated_by'];

    /**
     * A EquipmentStocktake has many items.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany('App\Models\Misc\Equipment\EquipmentStocktakeItem', 'stocktake_id');
    }

    /**
     * A EquipmentStocktake belongs to a Location
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo('App\Models\Misc\Equipment\EquipmentLocation', 'location_id');
    }

    /**
     * A EquipmentStocktake belongs to a User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }

    /**
     * EquipmentStocktake Summary
     *
     */
    public function summary()
    {
        $str = '';
        $excluded = '';
        foreach ($this->items as $item) {
            if ($item->qty_actual == NULL) {
                $excluded .= "$item->item_name, ";
            } else {
                if ($item->qty_expect > $item->qty_actual)
                    $str .= 'Missing '.($item->qty_expect - $item->qty_actual).' '. $item->item_name.'<br>';
                if ($item->qty_expect < $item->qty_actual)
                    $str .= 'Extra '.($item->qty_actual - $item->qty_expect).' '. $item->item_name.'<br>';
            }
        }
        $str = rtrim($str,'<br>');
        $excluded = ($excluded) ? "<br>Excluded from stocktake: ".rtrim($excluded, ', ') : '';
        return ($str) ? $str.$excluded : "All items accounted for $excluded";
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
                $table->company_id = 3; //Auth::user()->company_id;
            });

            // create a event to happen on updating
            static::updating(function ($table) {
                $table->updated_by = Auth::user()->id;
            });
        }
    }
}