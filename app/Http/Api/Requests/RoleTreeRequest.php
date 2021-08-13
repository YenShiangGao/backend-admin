<?php

namespace App\Http\Api\Requests;

/**
 * Class RoleTreeRequest
 * @package App\Http\Api\Requests
 */
class RoleTreeRequest extends FormRequest
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
            'role_id.*' => 'ROLE.TREE.INVALID_ROLE_ID',
        ];
    }
}
