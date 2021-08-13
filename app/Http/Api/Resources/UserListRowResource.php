<?php

namespace App\Http\Api\Resources;

use App\Http\Api\Resources\Traits\SuccessResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\Utils\Time;

/**
 * Class UserListRowResource
 * @package App\Http\Api\Resources
 */
class UserListRowResource extends JsonResource
{
    use SuccessResourceTrait;

    public function toArray($request)
    {
        $user = $this->resource;

        $lastOperationRecord = $user->lastOperationRecord;

        $lastLoginAt = '';
        $lastLoginRecord = $user->lastLoginRecord;
        if (filled($lastLoginRecord)) {
            $lastLoginAt = Time::fromTaipeiToEst($lastLoginRecord->last_login_at)->toDateTimeString();
        }

        return [
            'id'            => $user->id,
            'account'       => $user->account,
            'role_name'     => $user->role->name,
            'remark'        => $user->remark,
            'created_at'    => Time::fromTaipeiToEst($user->created_at)->toDateTimeString(),
            'last_login_at' => $lastLoginAt, // 最後登入時間
            'operator'      => filled($lastOperationRecord) ? $lastOperationRecord->operator->account : '',
            'status'        => array_search($user->status, config('constants.user.status')),
        ];
    }
}
