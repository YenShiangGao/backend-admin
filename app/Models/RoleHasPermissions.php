<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RoleHasPermissions
 * @package App\Models
 */
class RoleHasPermissions extends Model
{
    protected $table = 'role_has_permissions';
    public $incrementing = false;
    public $timestamps = false;

    protected $guarded = [];
}
