<?php

namespace App\Listeners;

use App\Repositories\RedisRepository;

/**
 * Class LoginRedisAuthListener
 * @package App\Listeners
 */
class LoginRedisAuthListener
{
    private $redisRepository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(RedisRepository $redisRepository)
    {
        $this->redisRepository = $redisRepository;
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        $info = $event->info;

        $this->redisRepository->setLoginAuth($info);
    }
}
