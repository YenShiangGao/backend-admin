<?php

namespace App\Http\Api\Requests;

/**
 * Class OperationListRequest
 * @package App\Http\Api\Requests
 */
class OperationListRequest extends FormRequest
{
    public function rules()
    {
        return [
            'category'   => 'nullable|alpha_dash',
            'project'    => 'nullable|alpha_dash',
            'subproject' => 'nullable|alpha_dash',
            'start_at'   => 'required_with:end_at|date_format:"Y-m-d"',
            'end_at'     => 'required_with:start_at|date_format:"Y-m-d"|after_or_equal:start_at',
            'account'    => 'nullable|between:6,30|alpha_num',
            'page'       => 'integer',
            'limit'      => 'integer|between:1,30',
        ];
    }

    public function messages()
    {
        return [
            'category.*'            => 'RECORD.OPERATION.INVALID_CATEGORY',
            'project.*'             => 'RECORD.OPERATION.INVALID_PROJECT',
            'subproject.*'          => 'RECORD.OPERATION.INVALID_SUBPROJECT',
            'start_at.*'            => 'RECORD.OPERATION.INVALID_START_AT',
            'end_at.after_or_equal' => 'RECORD.OPERATION.END_AT_AFTER-OR-EQUAL',
            'end_at.*'              => 'RECORD.OPERATION.INVALID_END_AT',
            'page.*'                => 'RECORD.OPERATION.INVALID_PAGE',
            'limit.*'               => 'RECORD.OPERATION.INVALID_LIMIT',
        ];
    }
}
