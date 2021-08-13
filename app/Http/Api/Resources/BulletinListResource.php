<?php

namespace App\Http\Api\Resources;

use App\Http\Api\Resources\Traits\SuccessResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * Class BulletinListResource
 * @package App\Http\Api\Resources
 */
class BulletinListResource extends JsonResource
{
    use SuccessResourceTrait;

    public function toArray($request)
    {
        $bulletin = $this->resource;

        return [
            'id'          => $bulletin->id,
            'type'        => $bulletin->type->name,
            'bulletin_at' => Carbon::parse($bulletin->created_at)->toDateString(),
            'subject'     => $bulletin->subject,
            'attachments' => BulletinAttachmentResource::collection($bulletin->attachments),
            'platforms'   => PlatformResource::collection($bulletin->platforms),
        ];
    }
}
