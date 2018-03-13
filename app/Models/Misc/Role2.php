<?php

namespace App\Models\Misc;

use DB;
use App\Models\Misc\Permission2;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Role2 extends Model {

    protected $table = 'roles';
    protected $fillable = ['name', 'slug', 'description', 'company_id', 'child', 'security'];


    /**
     * A Role belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\User', 'role_user', 'role_id', 'user_id');
    }

    /**
     * A Role belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany('App\Models\Misc\Permission2', 'permission_role', 'role_id', 'permission_id');
    }

    /**
     * Attach permission to a role.
     *
     * @param  $permission
     * @return int|bool
     */
    public function attachPermission($permission, $level, $company_id)
    {
        DB::table('permission_role')->insert(['role_id' => $this->id, 'permission_id' => $permission, 'level' => $level, 'company_id' => $company_id]);
    }

    /**
     * Detach permission from a role.
     *
     * @param $permission
     * @return int
     */
    public function detachPermission($permission)
    {
        return $this->permissions()->detach($permission);
    }

    /**
     * Detach all permissions.
     *
     * @return int
     */
    public function detachAllPermissions()
    {
        return $this->permissions()->detach();
    }

    /**
     * Determine level of a permission
     *
     * @param  $permission
     * @return int
     */
    public function permissionLevel($permission)
    {
        $permission_id = (is_int($permission)) ? $permission : Permission2::where('slug', $permission)->first()->id;
        $permssion_role = DB::table('permission_role')->where('permission_id', $permission_id)->where('role_id', $this->id)->first();

        return ($permssion_role) ? $permssion_role->level : 0;
    }

    /**
     * Set the name + create slug attributes  (mutator)
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = getUniqueSlug($this, $value);
    }


}