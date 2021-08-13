<?php

namespace App\Http\Api\Requests;

/**
 * Class BulletinFileUploadRequest
 * @package App\Http\Api\Requests
 */
class BulletinFileUploadRequest extends FormRequest
{
    public function rules()
    {
        return [
            'file' => 'required|file',
        ];
    }

    public function messages()
    {
        return [
            'file.*' => 'BULLETIN.FILE.UPLOAD.INVALID_FILE',
        ];
    }
}
