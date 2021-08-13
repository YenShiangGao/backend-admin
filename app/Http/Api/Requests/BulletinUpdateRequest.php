<?php

namespace App\Http\Api\Requests;

/**
 * Class BulletinUpdateRequest
 * @package App\Http\Api\Requests
 */
class BulletinUpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'bulletin_id'   => 'required|integer',
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
            'bulletin_id.*'   => 'BULLETIN.UPDATE.INVALID_BULLETIN_ID',
            'type_id.*'       => 'BULLETIN.UPDATE.INVALID_TYPE_ID',
            'subject.*'       => 'BULLETIN.UPDATE.INVALID_SUBJECT',
            'content.*'       => 'BULLETIN.UPDATE.INVALID_CONTENT',
            'attachments.*'   => 'BULLETIN.UPDATE.INVALID_ATTACHMENTS',
            'attachments.*.*' => 'BULLETIN.UPDATE.INVALID_ATTACHMENTS',
        ];
    }
}
