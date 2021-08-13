<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Redis;

/**
 * Class RedisRepository
 * @package App\Repositories
 */
class RedisRepository
{
    const USER_LOGIN_EXPIRE = 86405;

    /**
     * 寫入使用者login auth info
     *
     * @param array $attributes
     */
    public function setLoginAuth(array $attributes)
    {
        $userId = $attributes['user_id'];
        $key = "user_login:{$userId}";

        Redis::pipeline(function ($pipeline) use ($key, $attributes) {
            $pipeline->multi();
            $pipeline->hmset($key, $attributes);
            $pipeline->expire($key, self::USER_LOGIN_EXPIRE);
            $pipeline->exec();
        });
    }

    /**
     * 取得使用者login auth token
     *
     * @param int $userId
     * @return mixed
     */
    public function getLoginAuthToken(int $userId)
    {
        $key = "user_login:{$userId}";

        return Redis::hget($key, 'auth_token');
    }

    /**
     * 刪除使用者login auth
     *
     * @param int $userId
     * @return mixed
     */
    public function deleteLoginAuth(int $userId)
    {
        $key = "user_login:{$userId}";

        return Redis::DEL($key);
    }
}
