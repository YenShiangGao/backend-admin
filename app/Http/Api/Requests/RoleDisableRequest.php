<?php

namespace App\Http\Api\Requests;

/**
 * Class RoleDisableRequest
 * @package App\Http\Api\Requests
 */
class RoleDisableRequest extends FormRequest
{
    public function rules()
    {
        return [
            'role_id' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'role_id.*' => 'ROLE.DISABLE.INVALID_ROLE_ID',
        ];
    }
}
