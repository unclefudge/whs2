<?php

namespace App\Models\Site;

use Mail;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SiteAccident extends Model {

    protected $table = 'site_accidents';
    protected $fillable = [
        'site_id', 'date', 'name', 'company', 'age', 'occupation', 'location', 'nature',
        'referred', 'damage', 'info', 'action', 'notes', 'status',
        'resolved_at', 'created_by', 'updated_by', 'created_at', 'updated_at'
    ];

    protected $dates = ['date', 'resolved_at'];

    /**
     * A SiteAccident belongs to a site
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function site()
    {
        return $this->belongsTo('App\Models\Site\Site');
    }

    /**
     * A SiteAccident belongs to a user
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * Email Accident
     */
    public function emailAccident()
    {
        $site = Site::findOrFail($this->site_id);

        $email_to = [];
        if (\App::environment('prod')) {
            $email_roles = notificationsUsersEmailType('site.accident');
            $email_supers = $site->supervisorsEmails();
            $email_to = array_unique(array_merge($email_roles,$email_supers), SORT_REGULAR);
        }
        //$email_to[] = env('EMAIL_ME');
        $email_user = (validEmail(Auth::user()->email)) ?  Auth::user()->email : '';
        $data = [
            'id'                => $this->id,
            'site'              => $site->name . ' (' . $site->code . ')',
            'address'           => $site->address . ', ' . $site->SuburbStatePostcode,
            'date'              => $this->date->format('d/m/Y g:i a'),
            'worker'            => $this->name . ' (age: ' . $this->age . ')',
            'occupation'        => $this->occupation,
            'location'          => $this->location,
            'nature'            => $this->nature,
            'referred'          => $this->referred,
            'damage'            => $this->damage,
            'description'       => $this->info,
            'user_fullname'     => User::find($this->created_by)->fullname,
            'user_company_name' => User::find($this->created_by)->company->name,
            'submit_date'       => $this->created_at->format('d/m/Y g:i a'),
            'site_owner'        => $site->client->clientOfCompany->name,
        ];

        Mail::send('emails.siteAccident', $data, function ($m) use ($email_to, $email_user) {
            $m->from('do-not-reply@safeworksite.net');
            $m->to($email_to);
            if ($email_user)
                $m->cc($email_user);
            $m->subject('WHS Accident Notification');
        });
    }

    /**
     * Get the owner of record   (getter)
     *
     * @return string;
     */
    public function getOwnedByAttribute()
    {
        return $this->site->owned_by;
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
     * Set the resolved_at  (mutator)
     *
     *  - Fix for Carbon saving 0000-00-00 00:00:00 format
     *  - otherwise trys to save as -0001-11-30 06:12:32
     */
    /*
    public function setResolvedDateAttribute($date)
    {
        $date == "0000-00-00 00:00:00" ? "0000-00-00 00:00:00" : $date;
        $this->attributes['resolved_at'] = $date;
    }*/

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