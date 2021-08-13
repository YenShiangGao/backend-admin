<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Support\TraceInfo;

/**
 * Class UserLoginEvent
 * @package App\Events
 */
class UserLoginEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $info;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Request $request)
    {
        $this->info = [
            'user_id'    => $user->id,
            'ip'         => $request->ip(),
            'url'        => $request->url(),
            'login_at'   => now()->toDateTimeString(),
            'auth_token' => TraceInfo::getAuthToken(),
            'user_agent' => $request->userAgent(),
        ];
    }
}
