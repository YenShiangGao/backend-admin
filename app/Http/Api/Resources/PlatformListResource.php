<?php

namespace App\Http\Api\Resources;

use App\Http\Api\Resources\Traits\SuccessResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PlatformListResource
 * @package App\Http\Api\Resources
 */
class PlatformListResource extends JsonResource
{
    use SuccessResourceTrait;

    public function toArray($request)
    {
        $platform = $this->resource;

        $platformCurrency = $platform->currency ?? [];

        $currencies = collect(config('currency'))
            ->map(function ($currency) use ($platformCurrency) {
                return [
                    'code'   => $currency,
                    'status' => in_array($currency, $platformCurrency) ? 'enable' : 'disable',
                ];
            })->all();

        return [
            'code'               => $platform->code,
            'name'               => $platform->name,
            'currencies'         => $currencies,
            'agent_site_status'  => array_search(
                $platform->agent_site_status,
                config('constants.platform.agent_site.status')
            ),
            'member_site_status' => array_search(
                $platform->member_site_status,
                config('constants.platform.member_site.status')
            ),
        ];
    }
}
