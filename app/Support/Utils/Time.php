<?php

namespace App\Support\Utils;

use Illuminate\Support\Carbon;

/**
 * Class Time
 * @package App\Support\Utils
 */
class Time
{
    /**
     * +8時間 轉成 -4時間
     *
     * @param $datetime
     * @return Carbon
     */
    public static function fromTaipeiToEst($datetime)
    {
        return Carbon::parse($datetime, config('app.timezone'))
            ->timezone(config('app.east_timezone'));
    }

    /**
     * -4時間 轉成 +8時間
     *
     * @param $datetime
     * @return Carbon
     */
    public static function fromEstToTaipei($datetime)
    {
        return Carbon::parse($datetime, config('app.east_timezone'))
            ->timezone(config('app.timezone'));
    }

    public static function nowToEst()
    {
        return self::fromTaipeiToEst(now());
    }

    public static function now()
    {
        return now();
    }
}
