<?php

namespace App\Http\Api\Resources;

use App\Http\Api\Resources\Traits\SuccessResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class RoleLevelResource
 * @package App\Http\Api\Resources
 */
class RoleLevelResource extends JsonResource
{
    use SuccessResourceTrait;

    public function toArray($request)
    {
        return [
            'id'   => $this->resource->id,
            'name' => $this->resource->name,
        ];
    }
}
