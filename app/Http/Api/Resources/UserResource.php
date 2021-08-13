<?php

namespace App\Http\Api\Resources;

use App\Http\Api\Resources\Traits\SuccessResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Api\Resources
 */
class UserResource extends JsonResource
{
    use SuccessResourceTrait;

    public function toArray($request)
    {
        $user = $this->resource;

        return [
            'id'      => $user->id,
            'account' => $user->account,
            'role_id' => $user->role_id,
            'remark'  => $user->remark ?? '',
            'status'  => array_search($user->status, config('constants.user.status')),
        ];
    }
}
