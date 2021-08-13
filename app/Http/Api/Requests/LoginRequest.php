<?php

namespace App\Http\Api\Requests;

/**
 * Class LoginRequest
 * @package App\Http\Api\Requests
 */
class LoginRequest extends FormRequest
{
    public function rules()
    {
        return [
            'account'  => 'required|between:3,15|alpha_num',
            'password' => 'required|between:6,30|alpha_num',
        ];
    }

    public function messages()
    {
        return [
            'account.*'  => 'AUTH.LOGIN.INVALID_ACCOUNT',
            'password.*' => 'AUTH.LOGIN.INVALID_PASSWORD',
        ];
    }
}
