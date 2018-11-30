<?php

namespace App\Models\Comms;

use DB;
use Mail;
use App\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TodoUser extends Model {

    protected $table = 'todo_user';
    protected $fillable = [
        'todo_id', 'user_id', 'opened', 'opened_at'
    ];

    public $timestamps = false;
    protected $dates = ['opened_at'];

    /**
     * A TodoUser belongs to a ToDo
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function todo()
    {
        return $this->belongsTo('App\Models\Comms\Todo');
    }

    /**
     * A TodoUser belongs to a user
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }


    /**
     * Get the owner of record   (getter)
     *
     * @return string;
     */
    public function getOwnedByAttribute()
    {
        return $this->user_id;
    }


}