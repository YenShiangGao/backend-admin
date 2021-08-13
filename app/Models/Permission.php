<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * Class Permission
 * @package App\Models
 */
class Permission extends SpatiePermission
{
    protected $fillable = ['name', 'guard_name', 'remark'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
}
