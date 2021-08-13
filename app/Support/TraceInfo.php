<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Class TraceInfo
 * @package App\Support
 */
class TraceInfo
{
    private static $traceCode;
    private static $authToken;

    /**
     * @return string
     */
    public static function getTraceCode()
    {
        if (filled(self::$traceCode)) {
            return self::$traceCode;
        }

        self::$traceCode = md5(Str::uuid()->toString());

        return self::$traceCode;
    }

    /**
     * @return string
     */
    public static function getAuthToken()
    {
        if (filled(self::$authToken)) {
            return self::$authToken;
        }

        self::$authToken = auth()->check() ? md5(auth()->getToken()->get()) : '';

        return self::$authToken;
    }
}
