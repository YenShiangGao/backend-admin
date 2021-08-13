<?php

namespace App\Http\Api\Requests;

/**
 * Class UserListRequest
 * @package App\Http\Api\Requests
 */
class UserListRequest extends FormRequest
{
    public function rules()
    {
        return [
            'role_id' => 'integer',
            'account' => 'between:1,30|alpha_num',
            'page'    => 'integer',
            'limit'   => 'integer|between:1,30',
        ];
    }

    public function messages()
    {
        return [
            'role_id.*' => 'USER.LIST.INVALID_ROLE_ID',
            'account.*' => 'USER.LIST.INVALID_ACCOUNT',
            'page.*'    => 'USER.LIST.INVALID_PAGE',
            'limit.*'   => 'USER.LIST.INVALID_LIMIT',
        ];
    }
}
