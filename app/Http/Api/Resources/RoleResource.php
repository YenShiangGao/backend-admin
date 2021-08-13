<?php

namespace App\Http\Api\Resources;

use App\Http\Api\Resources\Traits\SuccessResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class RoleResource
 * @package App\Http\Api\Resources
 */
class RoleResource extends JsonResource
{
    use SuccessResourceTrait;

    public function toArray($request)
    {
        $role = $this->resource;

        return [
            'id'          => $role->id,
            'name'        => $role->name,
            'permissions' => PermissionResource::collection($role->getAllPermissions()),
            'level'       => RoleLevelResource::collection($role->level),
        ];
    }
}
