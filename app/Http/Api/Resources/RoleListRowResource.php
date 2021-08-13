<?php

namespace App\Http\Api\Resources;

use App\Http\Api\Resources\Traits\SuccessResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\Utils\Time;

/**
 * Class RoleListRowResource
 * @package App\Http\Api\Resources
 */
class RoleListRowResource extends JsonResource
{
    use SuccessResourceTrait;

    public function toArray($request)
    {
        $role = $this->resource;
        $lastOperationRecord = $role->lastOperationRecord;

        return [
            'id'           => $role->id,
            'name'         => $role->name,
            'created_at'   => Time::fromTaipeiToEst($role->created_at)->toDateTimeString(),
            'operator'     => filled($lastOperationRecord) ? $lastOperationRecord->operator->account : '',
            'can_delete'   => $role->canDelete(),
            'has_children' => $role->normal_children_count > 0,
        ];
    }
}
