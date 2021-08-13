<?php

namespace App\Http\Api\Resources;

use App\Http\Api\Resources\Traits\SuccessResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class BulletinAttachmentResource
 * @package App\Http\Api\Resources
 */
class BulletinAttachmentResource extends JsonResource
{
    use SuccessResourceTrait;

    public function toArray($request)
    {
        $attachment = $this->resource;

        return [
            'id'            => $attachment->id,
            'original_name' => $attachment->original_name,
            'file'          => $attachment->file,
        ];
    }
}
