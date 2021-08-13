<?php

namespace App\Http\Api\Requests;

use Illuminate\Validation\Rule;

/**
 * Class BulletinTypeStoreRequest
 * @package App\Http\Api\Requests
 */
class BulletinTypeStoreRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => [
                'required',
                'between:1,30',
                'string',
                Rule::unique('bulletin_type', 'name'),
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'BULLETIN.TYPE.STORE.UNIQUE_NAME',
            'name.*'      => 'BULLETIN.TYPE.STORE.INVALID_NAME',
        ];
    }
}
