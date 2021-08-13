<?php

namespace App\Http\Api\Resources;

use App\Http\Api\Resources\Traits\SuccessResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\Utils\Time;

/**
 * Class BulletinTypeListResource
 * @package App\Http\Api\Resources
 */
class BulletinTypeListResource extends JsonResource
{
    use SuccessResourceTrait;

    public function toArray($request)
    {
        $type = $this->resource;

        $lastOperationRecord = $type->lastOperationRecord;
        $lastRecordAt = $operator = '';
        if (filled($lastOperationRecord)) {
            $lastRecordAt = Time::fromTaipeiToEst($lastOperationRecord->last_login_at)->toDateTimeString();
            $operator = $lastOperationRecord->operator->account;
        }

        return [
            'id'         => $type->id,
            'name'       => $type->name,
            'updated_at' => $lastRecordAt,
            'operator'   => $operator,
        ];
    }
}
