<?php

namespace App\Models\Site\Planner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Trade extends Model {

    protected $table = 'trades';
    protected $fillable = ['name', 'status', 'company_id', 'created_by', 'updated_by'];

    /**
     * A Trade belongs to a company
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company() {
        return $this->belongsTo('App\Models\Company\Company');
    }

    /**
     * A Trade has many tasks
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tasks() {
        return $this->hasMany('App\Models\Site\Planner\Task');
    }

    /**
     * Get the owner of record  (getter)
     *
     * @return string;
     */
    public function getOwnedByAttribute()
    {
        return $this->company;
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

