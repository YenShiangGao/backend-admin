<?php

namespace App\Http\Api\Middleware;

use App\Http\Api\Resources\ErrorResource;
use Closure;

/**
 * Class CheckPermission
 * @package App\Http\Api\Middleware
 */
class CheckPermission
{
    public function handle($request, Closure $next, string ...$permissionName)
    {
        $role = auth()->user()->role;

        if ($role->isAdmin()) {
            return $next($request);
        }

        $rolePermissions = auth()->user()->role->getAllPermissions();

        // 檢查登入者是否有存取權限
        if (collect($rolePermissions)->whereIn('name', $permissionName)->isEmpty()) {
            return new ErrorResource('SYSTEM.PERMISSION_DENIED');
        }

        return $next($request);
    }
}
