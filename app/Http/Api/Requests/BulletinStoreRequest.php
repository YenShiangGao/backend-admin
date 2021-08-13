<?php

namespace App\Http\Api\Requests;

/**
 * Class BulletinStoreRequest
 * @package App\Http\Api\Requests
 */
class BulletinStoreRequest extends FormRequest
{
    public function rules()
    {
        return [
            'platforms'     => 'required|array',
            'platforms.*'   => 'alpha_num',
            'type_id'       => 'required|integer',
            'subject'       => 'required|between:1,50|string',
            'content'       => 'required|string',
            'attachments'   => 'nullable|array',
            'attachments.*' => 'integer|distinct',
        ];
    }

    public function messages()
    {
        return [
            'platforms.*'     => 'BULLETIN.STORE.INVALID_PLATFORMS',
            'platforms.*.*'   => 'BULLETIN.STORE.INVALID_PLATFORMS',
            'type_id.*'       => 'BULLETIN.STORE.INVALID_TYPE_ID',
            'subject.*'       => 'BULLETIN.STORE.INVALID_SUBJECT',
            'content.*'       => 'BULLETIN.STORE.INVALID_CONTENT',
            'attachments.*'   => 'BULLETIN.STORE.INVALID_ATTACHMENTS',
            'attachments.*.*' => 'BULLETIN.STORE.INVALID_ATTACHMENTS',
        ];
    }
}
