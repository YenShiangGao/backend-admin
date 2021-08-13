<?php

namespace App\Http\Api\Requests;

/**
 * Class BulletinListRequest
 * @package App\Http\Api\Requests
 */
class BulletinListRequest extends FormRequest
{
    public function rules()
    {
        return [
            'platform_code' => 'nullable|alpha_num',
            'type_id'       => 'nullable|integer',
            'subject'       => 'nullable|between:1,50|string',
            'start_at'      => 'required_with:end_at|date_format:"Y-m-d"',
            'end_at'        => 'required_with:start_at|date_format:"Y-m-d"|after_or_equal:start_at',
            'sort_by'       => 'in:desc,asc',
            'page'          => 'integer',
            'limit'         => 'integer|between:1,30',

        ];
    }

    public function messages()
    {
        return [
            'platform.*'            => 'BULLETIN.LIST.INVALID_PLATFORM',
            'type_id.*'             => 'BULLETIN.LIST.INVALID_TYPE_ID',
            'subject.*'             => 'BULLETIN.LIST.INVALID_SUBJECT',
            'start_at.*'            => 'BULLETIN.LIST.INVALID_START_AT',
            'end_at.after_or_equal' => 'BULLETIN.LIST.END_AT_AFTER-OR-EQUAL',
            'end_at.*'              => 'BULLETIN.LIST.INVALID_END_AT',
            'page.*'                => 'BULLETIN.LIST.INVALID_PAGE',
            'limit.*'               => 'BULLETIN.LIST.INVALID_LIMIT',
            'sort_by.*'             => 'BULLETIN.LIST.INVALID_SORT_BY',
        ];
    }
}
