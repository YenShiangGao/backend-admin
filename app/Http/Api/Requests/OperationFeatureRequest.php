<?php

namespace App\Http\Api\Requests;

/**
 * Class OperationFeatureRequest
 * @package App\Http\Api\Requests
 */
class OperationFeatureRequest extends FormRequest
{
    public function rules()
    {
        return [
            'code' => 'nullable|alpha_dash',
        ];
    }

    public function messages()
    {
        return [
            'code.*' => 'RECORD.OPERATION.FEATURES.INVALID_CODE',
        ];
    }
}
