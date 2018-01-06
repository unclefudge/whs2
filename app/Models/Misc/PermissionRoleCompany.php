<?php

namespace App\Models\Misc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PermissionRoleCompany extends Model {

    protected $table = 'permission_role_company';
    protected $fillable = ['permission_id', 'role_id', 'company_id'];

}

