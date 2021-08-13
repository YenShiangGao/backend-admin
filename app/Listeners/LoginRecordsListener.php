<?php

namespace App\Listeners;

use Jenssegers\Agent\Agent;
use App\Repositories\UserLoginRecordsRepository;

/**
 * Class LoginRecordsListener
 * @package App\Listeners
 */
class LoginRecordsListener
{
    private $userLoginRecordsRepository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(UserLoginRecordsRepository $userLoginRecordsRepository)
    {
        $this->userLoginRecordsRepository = $userLoginRecordsRepository;
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

        $agent = new Agent();
        $agent->setUserAgent($info['user_agent']);

        $device = '';
        $mobile = '';
        if ($agent->isDesktop()) {
            $device = 'pc';
        } elseif ($agent->isMobile()) {
            $device = 'mobile';
            $mobile = $agent->device();
        }

        $insert = [
            'user_id'     => $info['user_id'],
            'ip'          => $info['ip'],
            'url'         => $info['url'],
            'login_at'    => $info['login_at'],
            'auth_token'  => $info['auth_token'],
            'agent'       => $info['user_agent'] ?? null,
            'device'      => $device,
            'platform'    => $agent->platform() ?: '',
            'browser'     => $agent->browser() ?: '',
            'browser_ver' => $agent->version($agent->browser()) ?: '',
            'mobile'      => $mobile,
        ];

        $this->userLoginRecordsRepository->create($insert);
    }
}
