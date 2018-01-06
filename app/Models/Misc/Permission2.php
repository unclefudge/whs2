<?php

namespace App\Models\Misc;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Permission2 extends Model {

    protected $table = 'permissions';
    protected $fillable = ['name', 'slug', 'description', 'model'];



    /**
     * A Permission belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\User', 'permission_user', 'permission_id', 'user_id');
    }

    /**
     * A Permission belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('App\User', 'permission_role', 'permission_id', 'role_id');
    }
}