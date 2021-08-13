<?php

namespace App\Http\Api\Resources;

use App\Http\Api\Resources\Traits\SuccessResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PermissionResource
 * @package App\Http\Api\Resources
 */
class PermissionResource extends JsonResource
{
    use SuccessResourceTrait;

    public function toArray($request)
    {
        $permission = explode(":", $this->resource->name);

        $group = $permission[0] ?? '';
        $name = $permission[1] ?? '';

        return [
            'id'     => $this->resource->id,
            'group'  => $group,
            'name'   => $name,
            'remark' => $this->resource->remark,
        ];
    }
}

