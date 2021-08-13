<?php

namespace App\Http\Api\Resources;

use App\Http\Api\Resources\Traits\SuccessResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class RoleListResource
 * @package App\Http\Api\Resources
 */
class RoleListResource extends JsonResource
{
    use SuccessResourceTrait;

    public function toArray($request)
    {
        return [
            'level' => RoleLevelResource::collection($this->resource['level']),
            'roles' => RoleListRowResource::collection($this->resource['roles']),
        ];
    }
}
