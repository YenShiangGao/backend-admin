<?php

namespace App\Listeners;

use App\Repositories\RedisRepository;

/**
 * Class LogoutRedisAuthListener
 * @package App\Listeners
 */
class LogoutRedisAuthListener
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
        $this->redisRepository->deleteLoginAuth($event->user->id);
    }
}
