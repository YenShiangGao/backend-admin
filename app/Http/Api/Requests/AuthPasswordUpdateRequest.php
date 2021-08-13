<?php

namespace App\Http\Api\Requests;

/**
 * Class AuthPasswordUpdateRequest
 * @package App\Http\Api\Requests
 */
class AuthPasswordUpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'password'     => 'required|between:6,30|alpha_num',
            'old_password' => 'required|between:6,30|alpha_num',
        ];
    }

    public function messages()
    {
        return [
            'password.*'     => 'AUTH.PASSWORD.UPDATE.INVALID_PASSWORD',
            'old_password.*' => 'AUTH.PASSWORD.UPDATE.INVALID_PASSWORD',
        ];
    }
}
