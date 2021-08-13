<?php

namespace App\Http\Api\Requests;

/**
 * Class UserDetailRequest
 * @package App\Http\Api\Requests
 */
class UserDetailRequest extends FormRequest
{
    public function rules()
    {
        return [
            'user_id'  => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'user_id.*'  => 'USER.DETAIL.INVALID_USER_ID',
        ];
    }
}
