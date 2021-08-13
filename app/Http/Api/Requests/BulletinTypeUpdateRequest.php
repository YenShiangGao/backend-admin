<?php

namespace App\Http\Api\Requests;

/**
 * Class BulletinTypeUpdateRequest
 * @package App\Http\Api\Requests
 */
class BulletinTypeUpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'type_id' => 'required|integer',
            'name'    => 'required|between:1,30|string',
        ];
    }

    public function messages()
    {
        return [
            'type_id.*' => 'BULLETIN.TYPE.UPDATE.INVALID_TYPE_ID',
            'name.*'    => 'BULLETIN.TYPE.UPDATE.INVALID_NAME',
        ];
    }
}
