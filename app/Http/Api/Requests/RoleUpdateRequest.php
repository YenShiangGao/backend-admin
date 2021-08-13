<?php

namespace App\Http\Api\Requests;

/**
 * Class RoleUpdateRequest
 * @package App\Http\Api\Requests
 */
class RoleUpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'role_id'       => 'required|integer',
            'name'          => 'required|between:1,30|string',
            'permissions'   => 'required|array',
            'permissions.*' => 'integer|distinct', // 檢查permissions array的value是否為整數
        ];
    }

    public function messages()
    {
        return [
            'role_id.*'       => 'ROLE.UPDATE.INVALID_ROLE_ID',
            'name.*'          => 'ROLE.UPDATE.INVALID_NAME',
            'permissions.*'   => 'ROLE.UPDATE.INVALID_PERMISSIONS',
            'permissions.*.*' => 'ROLE.UPDATE.INVALID_PERMISSIONS',
        ];
    }
}
