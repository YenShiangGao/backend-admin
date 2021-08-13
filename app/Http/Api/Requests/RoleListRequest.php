<?php

namespace App\Http\Api\Requests;

/**
 * Class RoleListRequest
 * @package App\Http\Api\Requests
 */
class RoleListRequest extends FormRequest
{
    public function rules()
    {
        return [
            'role_id' => 'integer',
            'name'    => 'between:1,30|string',
            'page'    => 'integer',
            'limit'   => 'integer|between:1,30',
        ];
    }

    public function messages()
    {
        return [
            'role_id.*' => 'ROLE.LIST.INVALID_ROLE_ID',
            'name.*'    => 'ROLE.LIST.INVALID_NAME',
            'page.*'    => 'ROLE.LIST.INVALID_PAGE',
            'limit.*'   => 'ROLE.LIST.INVALID_LIMIT',
        ];
    }
}
