<?php

namespace App\Models\Safety;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class WmsStep extends Model {

    protected $table = 'wms_steps';
    protected $fillable = [
        'doc_id', 'name', 'order', 'likelihood', 'consequence', 'rating',
        'master', 'master_id', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    /**
     * A Step belongs to a WMSdoc
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function document()
    {
        return $this->belongsTo('App\Models\Safety\WmsDoc', 'doc_id');
    }

    /**
     * A WMS Step has many Hazards.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hazards() {
        return $this->hasMany('App\Models\Safety\WmsHazard', 'step_id');
    }

    /**
     * A WMS Step has many Controls.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function controls() {
        return $this->hasMany('App\Models\Safety\WmsControl', 'step_id');
    }

    /**
     * Get the owner of record   (getter)
     *
     * @return string;
     */
    public function getOwnedByAttribute()
    {
        return $this->document->owned_by;
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

