<?php

namespace App\Http\Api\Resources;

use App\Http\Api\Resources\Traits\SuccessResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class OperationFeatureResource
 * @package App\Http\Api\Resources
 */
class OperationFeatureResource extends JsonResource
{
    use SuccessResourceTrait;

    public function toArray($request)
    {
        $feature = $this->resource;

        return [
            'type' => $feature['type'],
            'code' => $feature['code'],
        ];
    }
}
