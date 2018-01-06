<?php

namespace App\Models\Safety;

use DB;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SafetyDocCategory extends Model {

    protected $table = 'safety_docs_categories';
    protected $fillable = [
        'type', 'name', 'parent', 'company_id',
        'status', 'created_by', 'updated_by'];

    /**
     * A Category belongs to a Company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company() {
        return $this->belongsTo('App\Models\Company\Company', 'company_id');
    }

    /**
     * A Category has many documents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documents() {
        return $this->hasMany('App\Models\Safety\SafetyDoc', 'category_id');
    }

    /**
     * A Category Listing for SDS.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    static public function sdsCats($all = '') {

        $cats = DB::table('safety_docs_categories')->select('id', 'name')->where('type', 'SDS')->where('status', '1')->get();
        $categories = [];
        foreach ($cats as $cat)
            $categories[$cat->id] = $cat->name;
        asort($categories);
        if ($all)
            $categories = [' ' => 'All'] + $categories;
        else
            $categories = ['' => 'Select Category'] + $categories;

        return $categories;
    }



    /**
     * Get the owner of record   (getter)
     *
     * @return string;
     */
    public function getOwnedByAttribute()
    {
        return $this->company;
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

