<?php

namespace App\Models\Misc\Equipment;

use DB;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Equipment extends Model {

    protected $table = 'equipment';
    protected $fillable = ['name', 'purchased', 'disposed', 'status', 'company_id', 'created_by', 'created_at', 'updated_at', 'updated_by'];

    /**
     * A Equipment has many LocationItems.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function locationItems()
    {
        return $this->hasMany('App\Models\Misc\Equipment\EquipmentLocationItem');
    }

    /**
     * A Equipment has many logs.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function log()
    {
        return $this->hasMany('App\Models\Misc\Equipment\EquipmentLog');
    }

    /**
     * A Equipment has many logs.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function lost()
    {
        return $this->hasMany('App\Models\Misc\Equipment\EquipmentLost');
    }

    /**
     * A list of location of the items separated by ,
     *
     * @return string
     */
    public function locationsSBC()
    {
        $string = '';
        foreach ($this->locationItems as $item) {
            $string .= ($item->location->site_id) ? $item->location->site->name . ', ' : $item->location->other;
        }

        return rtrim($string, ', ');
    }

    /**
     * Get the Item Total  (getter)
     */
    public function getTotalAttribute()
    {
        return DB::table('equipment_location_items')->where('equipment_id', $this->id)->sum('qty');
    }

    /**
     * Get the # Missing  (getter)
     */
    public function getTotalLostAttribute()
    {
        return DB::table('equipment_lost')->where('equipment_id', $this->id)->sum('qty');
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
                $table->company_id = Auth::user()->company_id;
            });

            // create a event to happen on updating
            static::updating(function ($table) {
                $table->updated_by = Auth::user()->id;
            });
        }
    }
}