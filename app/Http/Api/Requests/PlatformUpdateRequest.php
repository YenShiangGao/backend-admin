<?php

namespace App\Http\Api\Requests;

use Illuminate\Validation\Rule;

/**
 * Class PlatformUpdateRequest
 * @package App\Http\Api\Requests
 */
class PlatformUpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'platform_code'      => 'required|alpha_num',
            'currencies'         => 'array',
            'currencies.*'       => Rule::in(config('currency')),
            'agent_site_status'  => [
                'alpha_dash',
                Rule::in(array_keys(config('constants.platform.agent_site.status')))
            ],
            'member_site_status' => [
                'alpha_dash',
                Rule::in(array_keys(config('constants.platform.member_site.status')))
            ],
        ];
    }

    public function messages()
    {
        return [
            'platform_code.*'      => 'PLATFORM.UPDATE.INVALID_CODE',
            'currencies.*'         => 'PLATFORM.UPDATE.INVALID_CURRENCY',
            'currencies.*.*'       => 'PLATFORM.UPDATE.INVALID_CURRENCY',
            'agent_site_status.*'  => 'PLATFORM.UPDATE.INVALID_AGENT_SITE_STATUS',
            'member_site_status.*' => 'PLATFORM.UPDATE.INVALID_MEMBER_SITE_STATUS',
        ];
    }
}
