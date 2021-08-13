<?php

namespace App\Http\Api\Requests;

/**
 * Class BulletinDisableRequest
 * @package App\Http\Api\Requests
 */
class BulletinDisableRequest extends FormRequest
{
    public function rules()
    {
        return [
            'bulletin_id' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'bulletin_id.*' => 'BULLETIN.DISABLE.INVALID_BULLETIN_ID',
        ];
    }
}
