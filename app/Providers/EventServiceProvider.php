<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\UserLoginEvent' => [
            'App\Listeners\LoginRedisAuthListener',
            'App\Listeners\LoginRecordsListener',
        ],
        'App\Events\UserLogoutEvent' => [
            'App\Listeners\LogoutRedisAuthListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    protected $subscribe = [
//        'App\Listeners\UserRecordsListener',
    ];
}
