<?php

namespace App\Http\Api\Requests;

use Illuminate\Validation\Rule;

/**
 * Class PlatformStoreRequest
 * @package App\Http\Api\Requests
 */
class PlatformStoreRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string',
            'code' => [
                'required',
                'alpha_num',
                Rule::unique('platform', 'code'),
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.*'      => 'PLATFORM.STORE.INVALID_NAME',
            'code.unique' => 'PLATFORM.STORE.UNIQUE_CODE',
            'code.*'      => 'PLATFORM.STORE.INVALID_CODE',
        ];
    }
}
