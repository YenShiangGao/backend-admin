<?php

namespace App\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Api\Resources\Traits\SuccessResourceTrait;

/**
 * Class LoginResource
 * @package App\Http\Api\Resources
 */
class LoginResource extends JsonResource
{
    use SuccessResourceTrait;

    public function toArray($request)
    {
        return [
            'auth' => [
                'type'  => 'Bearer',
                'token' => $this->resource['token'],
            ],
        ];
    }
}
