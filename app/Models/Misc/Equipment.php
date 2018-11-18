<?php

namespace App\Models\Misc;

use DB;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Equipment extends Model {

    protected $table = 'equipment';
    protected $fillable = ['name', 'company_id', 'created_by', 'created_at', 'updated_at', 'updated_by'];

    /**
     * A Equipment has many locations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function locations()
    {
        return $this->hasMany('App\Models\Misc\EquipmentLocation', 'item_id');
    }

    /**
     * A Equipment has many transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function transactions()
    {
        return $this->hasMany('App\Models\Misc\EquipmentTransaction', 'item_id');
    }

    /**
     * A list of users that supervise the company separated by ,
     *
     * @return string
     */
    public function locationsSBC()
    {
        $string = '';
        foreach ($this->locations as $loc) {
            $string .= ($loc->site_id) ? $loc->site->suburb . ', ' : $loc->other;
        }

        return rtrim($string, ', ');
    }

    /**
     * Get the Item Total  (getter)
     */
    public function getTotalAttribute()
    {
        return DB::table('equipment_location')->where('item_id', $this->id)->sum('qty');
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