<?php

namespace App\Models\Site\Planner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Task extends Model
{
    protected $table = 'trade_task';
    protected $fillable = ['name', 'code', 'upcoming', 'status', 'trade_id', 'company_id', 'created_by', 'updated_by'];


    /**
     * A Tasks belongs to a trade
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function trade() {
        return $this->belongsTo('App\Models\Site\Planner\Trade');
    }

    /**
     * Get the owner of record  (getter)
     *
     * @return string;
     */
    public function getOwnedByAttribute()
    {
        return $this->trade->company;
    }

    /**
     * The "booting" method of the model.
     *
     * Overrides parent function
     *
     * @return void
     */
    public static function boot() {
        parent::boot();

        if(Auth::check()) {
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
