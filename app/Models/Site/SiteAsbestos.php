<?php

namespace App\Models\Site;

use URL;
use Mail;
use App\Models\Misc\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use nilsenj\Toastr\Facades\Toastr;

class SiteAsbestos extends Model {

    protected $table = 'site_asbestos';
    protected $fillable = [
        'site_id', 'amount', 'friable', 'type', 'location', 'date_from', 'date_to', 'hours_from', 'hours_to', 'workers',
        'equip_overalls', 'equip_mask', 'equip_gloves', 'equip_half_face', 'equip_full_face', 'equip_other',
        'method_fencing', 'method_signage', 'method_water', 'method_pva', 'method_barriers', 'method_plastic', 'method_vacuum', 'method_other',
        'isolation', 'register', 'swms', 'inspection', 'supervisor_id',
        'attachment', 'company_id', 'status', 'resolved_at',
        'created_by', 'updated_by', 'created_at', 'updated_at',
    ];

    protected $dates = ['date_from', 'date_to', 'resolved_at'];

    /**
     * A SiteAsbestos belongs to a site
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function site()
    {
        return $this->belongsTo('App\Models\Site\Site');
    }

    /**
     * A SiteAsbestos belongs to a user
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }


    /**
     * A SiteAsbestos has many Actions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function actions()
    {
        return $this->hasMany('App\Models\Misc\Action', 'table_id')->where('table', $this->table);
    }

    /**
     * Equipment used
     */
    public function equipment($format)
    {
        $str = '';
        if ($format == 'SBC') {
            if ($this->equip_overalls) $str .= 'Protective coveralls, ';
            if ($this->equip_gloves) $str .= 'Protective gloves, ';
            if ($this->equip_mask) $str .= 'P2 Mask, ';
            if ($this->equip_half_face) $str .= '1/2 face respirator, ';
            if ($this->equip_full_face) $str .= 'Full face air supplied, ';
            if ($this->equip_other) $str .= $this->equip_other;
        } else {
            if ($this->equip_overalls) $str .= "<div class='col-md-6' style='padding-left: 0px'>&bull; Protective coveralls</div>";
            if ($this->equip_gloves) $str .= "<div class='col-md-6' style='padding-left: 0px'>&bull; Protective gloves</div>";
            if ($this->equip_mask) $str .= "<div class='col-md-6' style='padding-left: 0px'>&bull; P2 Mask</div>";
            if ($this->equip_half_face) $str .= "<div class='col-md-6' style='padding-left: 0px'>&bull; 1/2 face respirator</div>";
            if ($this->equip_full_face) $str .= "<div class='col-md-6' style='padding-left: 0px'>&bull; Full face air supplied</div>";
            if ($this->equip_other) $str .= "<div class='col-md-6' style='padding-left: 0px'>&bull; " . $this->equip_other . "</div>";
        }

        return rtrim($str, ', ');
    }

    /**
     * Methods used
     */
    public function methods($format)
    {
        $str = '';
        if ($format == 'SBC') {
            if ($this->method_fencing) $str .= 'Fencing, ';
            if ($this->method_signage) $str .= 'Signage, ';
            if ($this->method_water) $str .= 'Water, ';
            if ($this->method_pva) $str .= 'PVA, ';
            if ($this->method_barriers) $str .= 'Barriers, ';
            if ($this->method_plastic) $str .= '200 μm plastic, ';
            if ($this->method_vacuum) $str .= 'Class H asbestos vacuum cleaners, ';
            if ($this->method_other) $str .= $this->method_other;
        } else {
            if ($this->method_fencing) $str .= "<div class='col-md-6' style='padding-left: 0px'>&bull; Fencing</div>";
            if ($this->method_signage) $str .= "<div class='col-md-6' style='padding-left: 0px'>&bull; Signage</div>";
            if ($this->method_water) $str .= "<div class='col-md-6' style='padding-left: 0px'>&bull; Water</div>";
            if ($this->method_pva) $str .= "<div class='col-md-6' style='padding-left: 0px'>&bull; PVA</div>";
            if ($this->method_barriers) $str .= "<div class='col-md-6' style='padding-left: 0px'>&bull; Barriers</div>";
            if ($this->method_plastic) $str .= "<div class='col-md-6' style='padding-left: 0px'>&bull; 200 μm plastic</div>";
            if ($this->method_vacuum) $str .= "<div class='col-md-6' style='padding-left: 0px'>&bull; Class H asbestos vacuum cleaners</div>";
            if ($this->method_other) $str .= "<div class='col-md-6' style='padding-left: 0px'>&bull; " . $this->method_other . "</div>";
        }

        return rtrim($str, ', ');
    }

    /**
     * Update Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function updateStatus($status)
    {
        $old = $this->status;
        $this->status = $status;
        $this->save();

        if ($old != $status) {
            $mesg = ($status) ? 'Re-opened Notification' : 'Closed Notification';
            $action = Action::create(['action' => $mesg, 'table' => $this->table, 'table_id' => $this->id]);
            $this->emailAction($action, 'important');
        }
    }
    /**
     * Email Notification
     */
    public function emailNotification()
    {
        $site = Site::findOrFail($this->site_id);

        if (\App::environment('prod')) {
            $email_roles = $site->owned_by->notificationsUsersEmailType('site.asbestos'); 
            $email_supers = $site->supervisorsEmails();
            $email_to = array_unique(array_merge($email_roles, $email_supers), SORT_REGULAR);
        } else
            $email_to = [env('EMAIL_ME')];
        $email_user = (validEmail(Auth::user()->email)) ? Auth::user()->email : '';

        $data = [
            'id'                => $this->id,
            'site'              => $site->name . ' (' . $site->code . ')',
            'address'           => $site->address . ', ' . $site->SuburbStatePostcode,
            'dates'             => $this->date_from->format('d/m/Y') . ' to ' . $this->date_to->format('d/m/Y'),
            'amount'            => $this->amount,
            'class'             => ($this->friable) ? 'Class A (Friable)' : 'Class B (Non-Friable)',
            'type'              => $this->type,
            'location'          => $this->location,
            'url'               => URL::to('/site/asbestos/' . $this->id),
            'user_fullname'     => Auth::user()->fullname,
            'user_company_name' => Auth::user()->company->name,
            'reason'            => $this->reason,
            'site_owner'        => $site->client->clientOfCompany->name,
        ];

        Mail::send('emails/siteAsbestos', $data, function ($m) use ($email_to, $email_user) {
            $m->from('do-not-reply@safeworksite.com.au');
            $m->to($email_to);
            if ($email_user)
                $m->cc($email_user);
            $m->subject('Asbestos Notification');
        });
    }

    /**
     * Email Action Notification
     */
    public function emailAction($action)
    {
        $site = Site::findOrFail($this->site_id);

        if (\App::environment('prod')) {
            $email_roles = $site->owned_by->notificationsUsersEmailType('site.asbestos');
            $email_supers = $site->supervisorsEmails();
            $email_to = array_unique(array_merge($email_roles, $email_supers), SORT_REGULAR);
        } else
            $email_to = [env('EMAIL_ME')];

        $email_user = (validEmail(Auth::user()->email)) ? Auth::user()->email : '';
        $data = [
            'id'                => $this->id,
            'site'              => $site->name . ' (' . $site->code . ')',
            'date'              => Carbon::now()->format('d/m/Y g:i a'),
            'user_fullname'     => Auth::user()->fullname,
            'user_company_name' => Auth::user()->company->name,
            'action'            => $action->action,
            'url'               => URL::to('/site/asbestos/' . $this->id),
            'site_owner'        => $site->client->clientOfCompany->name,
        ];

        Mail::send('emails/siteAsbestosAction', $data, function ($m) use ($email_to, $email_user) {
            $m->from('do-not-reply@safeworksite.com.au');
            $m->to($email_to);
            if ($email_user)
                $m->cc($email_user);
            $m->subject('Asbestos Update Notification');
        });
    }

    /**
     * Set the resolved_at  (mutator)
     *
     *  - Fix for Carbon saving 0000-00-00 00:00:00 format
     *  - otherwise trys to save as -0001-11-30 06:12:32
     */
    /*
   public function setResolvedAtAttribute($date) {
       $date == "0000-00-00 00:00:00" ? "0000-00-00 00:00:00" : $date;
       $this->attributes['resolved_at'] = $date;
    }*/

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