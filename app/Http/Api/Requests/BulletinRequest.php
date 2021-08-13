<?php

namespace App\Http\Api\Requests;

/**
 * Class BulletinRequest
 * @package App\Http\Api\Requests
 */
class BulletinRequest extends FormRequest
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
            'bulletin_id.*' => 'BULLETIN.DETAIL.INVALID_BULLETIN_ID',
        ];
    }
}
