<?php

namespace App\Models\Misc;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Client extends Model {

    protected $table = 'clients';
    protected $fillable = [
        'name', 'slug', 'email', 'phone',
        'address', 'address2', 'suburb', 'state', 'postcode', 'country',
        'notes', 'company_id',
        'status', 'created_by', 'updated_by'];

    /**
     * A Client belongs to a Company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function clientOfCompany() {
        return $this->belongsTo('App\Models\Company\Company', 'company_id');
    }

    /**
     * A Client has many sites.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sites() {
        return $this->hasMany('App\Models\Site\Site');
    }


    /**
     * A list of sites that client has separated by ,
     *
     * @return string
     */
    public function sitesSBC() {

        $client_sites = '';
        foreach ($this->sites as $site) {
            $client_sites .= $site->name . ', ';
        }
        return rtrim($client_sites, ', ');
    }

    /**
     * Get the owner of record  (getter)
     *
     * @return string;
     */
    public function getOwnedByAttribute()
    {
        return $this->clientOfCompany;
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
     * Set the name + create slug attributes  (mutator)
     *
     * @param $value
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucfirst($value);
        $this->attributes['slug'] = getUniqueSlug($this, $value);
    }

    /**
     * Set the suburb to uppercase format  (mutator)
     *
     * @param $value
     */
    public function setSuburbAttribute($value)
    {
        $this->attributes['suburb'] = strtoupper($value);
    }

    /**
     * Set the phone number to AU format  (mutator)
     *
     * @param $phone
     */
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = format_phone('au', $value);
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

