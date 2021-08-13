<?php

namespace App\Http\Api\Resources;

use App\Http\Api\Resources\Traits\SuccessResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * Class BulletinResource
 * @package App\Http\Api\Resources
 */
class BulletinResource extends JsonResource
{
    use SuccessResourceTrait;

    public function toArray($request)
    {
        $bulletin = $this->resource;

        return [
            'id'          => $bulletin->id,
            'type_id'     => $bulletin->type->id,
            'bulletin_at' => Carbon::parse($bulletin->created_at)->toDateString(),
            'subject'     => $bulletin->subject,
            'content'     => $bulletin->content,
            'attachments' => BulletinAttachmentResource::collection($bulletin->attachments),
            'platforms'   => PlatformResource::collection($bulletin->platforms),
        ];
    }
}
