<?php

namespace App\Http\Api\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use App\Http\Api\Resources\ErrorResource;
use App\Repositories\RedisRepository;

/**
 * Class Authenticate
 * @package App\Http\Api\Middleware
 */
class Authenticate extends Middleware
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @param mixed ...$guards
     * @return ErrorResource|mixed|string|null]
     */
    public function handle($request, Closure $next, ...$guards)
    {
        if (!auth()->check()) {
            return new ErrorResource('SYSTEM.NOT_LOGGED_IN');
        }

        // 重複登入檢查
        if (!$this->checkRedisAuth()) {
            auth()->logout();

            return new ErrorResource('SYSTEM.NOT_LOGGED_IN');
        }

        return $next($request);
    }

    /**
     * 檢查header token是否與redis紀錄符合, 避免相同帳號重複登入
     *
     * @return bool
     */
    private function checkRedisAuth()
    {
        $userId = auth()->user()->id;
        $redisAuthToken = resolve(RedisRepository::class)->getLoginAuthToken($userId);

        if (blank($redisAuthToken)) {
            return false;
        }

        $token = md5(auth()->getToken()->get());

        return $redisAuthToken === $token;
    }
}
