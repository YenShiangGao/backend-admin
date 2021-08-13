<?php

namespace App\Http\Api\Requests;

use Illuminate\Validation\Rule;

/**
 * Class UserStoreRequest
 * @package App\Http\Api\Requests
 */
class UserStoreRequest extends FormRequest
{
    public function rules()
    {
        return [
            'account'  => [
                'required',
                'between:6,15',
                'alpha_num',
                Rule::unique('user', 'account'),
            ],
            'password' => 'required|between:6,30|alpha_num',
            'role_id'  => 'required|integer',
            'remark'   => 'nullable|between:0,50|string',
            'status'   => ['required', Rule::in(array_keys(config('constants.user.status')))],
        ];
    }

    public function messages()
    {
        return [
            'account.unique' => 'USER.STORE.UNIQUE_ACCOUNT',
            'account.*'      => 'USER.STORE.INVALID_ACCOUNT',
            'password.*'     => 'USER.STORE.INVALID_PASSWORD',
            'role_id.*'      => 'USER.STORE.INVALID_ROLE_ID',
            'remark.*'       => 'USER.STORE.INVALID_REMARK',
            'status.*'       => 'USER.STORE.INVALID_STATUS',
        ];
    }
}
