<?php

namespace App\Http\Api\Resources;

use App\Http\Api\Resources\Traits\SuccessResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PlatformResource
 * @package App\Http\Api\Resources
 */
class PlatformResource extends JsonResource
{
    use SuccessResourceTrait;

    public function toArray($request)
    {
        $platform = $this->resource;

        return [
            'code'   => $platform['code'],
            'name' => $platform['name'],
        ];
    }
}
