<?php

namespace App\Http\Api\Requests;

/**
 * Class RoleRequest
 * @package App\Http\Api\Requests
 */
class RoleRequest extends FormRequest
{
    public function rules()
    {
        return [
            'role_id' => 'integer',
        ];
    }

    public function messages()
    {
        return [
            'role_id.*' => 'ROLE.DETAIL.INVALID_ROLE_ID',
        ];
    }
}
