<?php

namespace App\Http\Api\Requests;

use Illuminate\Validation\Rule;

/**
 * Class UserUpdateRequest
 * @package App\Http\Api\Requests
 */
class UserUpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'user_id'  => 'required|integer',
            'role_id'  => 'integer',
            'password' => 'nullable|between:6,30|alpha_num',
            'remark'   => 'nullable|between:0,50|string',
            'status'   => Rule::in(array_keys(config('constants.user.status'))),
        ];
    }

    public function messages()
    {
        return [
            'user_id.*'  => 'USER.UPDATE.INVALID_USER_ID',
            'role_id.*'  => 'USER.UPDATE.INVALID_ROLE_ID',
            'password.*' => 'USER.UPDATE.INVALID_PASSWORD',
            'remark.*'   => 'USER.UPDATE.INVALID_REMARK',
            'status.*'   => 'USER.UPDATE.INVALID_STATUS',
        ];
    }
}
