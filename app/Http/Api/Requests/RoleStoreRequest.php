<?php

namespace App\Http\Api\Requests;

use Illuminate\Validation\Rule;

/**
 * Class RoleStoreRequest
 * @package App\Http\Api\Requests
 */
class RoleStoreRequest extends FormRequest
{
    public function rules()
    {
        return [
            'parent_role_id' => 'required|integer',
            'name'           => [
                'required',
                'between:1,30',
                'string',
                Rule::unique('roles', 'name'),
            ],
            'permissions'    => 'required|array',
            'permissions.*'  => 'integer', // 檢查permissions array的value是否為整數
        ];
    }

    public function messages()
    {
        return [
            'parent_role_id.*' => 'ROLE.STORE.INVALID_PARENT_ROLE_ID',
            'name.unique'      => 'ROLE.STORE.UNIQUE_NAME',
            'name.*'           => 'ROLE.STORE.INVALID_NAME',
            'permissions.*'    => 'ROLE.STORE.INVALID_PERMISSIONS',
            'permissions.*.*'  => 'ROLE.STORE.INVALID_PERMISSIONS',
        ];
    }
}
