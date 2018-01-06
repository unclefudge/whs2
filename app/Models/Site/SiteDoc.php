<?php

namespace App\Models\Site;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SiteDoc extends Model {

    protected $table = 'site_docs';
    protected $fillable = [
        'type', 'category_id', 'site_id', 'name', 'attachment',
        'reference', 'version', 'notes', 'for_company_id', 'company_id', 'share',
        'status', 'created_by', 'updated_by'];

    /**
     * A Document belongs to a Company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company() {
        return $this->belongsTo('App\Models\Company\Company', 'company_id');
    }

    /**
     * A Document belongs to a Category.  (sometimes - Some Docs do)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category() {
        return $this->belongsTo('App\Models\ReportCategory', 'category_id');
    }

    /**
     * A Document belongs to a Site.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function site() {
        return $this->belongsTo('App\Models\Site\Site', 'site_id');
    }

    /**
     * Get the Attachment URL (setter)
     */
    public function getAttachmentUrlAttribute()
    {
        if ($this->attributes['attachment'])
            return '/filebank/site/'.$this->attributes['site_id']."/docs/".$this->attributes['attachment'];
        return '';
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