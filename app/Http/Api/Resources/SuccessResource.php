<?php

namespace App\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Api\Resources\Traits\SuccessResourceTrait;

/**
 * Class SuccessResource
 * @package App\Http\Api\Resources
 */
class SuccessResource extends JsonResource
{
    use SuccessResourceTrait;

    public function __construct($resource = [])
    {
        parent::__construct($resource);
    }

    public function toArray($request)
    {
        return $this->resource;
    }
}
