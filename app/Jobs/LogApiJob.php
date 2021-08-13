<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use App\Repositories\LogApiUserRepository;

/**
 * Class LogApiJob
 * @package App\Jobs
 */
class LogApiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $log;

    private const API_USER_LIST = 'userList';
    private const API_USER_STORE = 'userStore';
    private const API_USER_UPDATE = 'userUpdate';

    /**
     * LogApiJob constructor.
     * @param Collection $log
     */
    public function __construct(Collection $log)
    {
        $this->log = $log;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $action = $this->getLog('action');

        switch ($action) {
            case self::API_USER_STORE:
            case self::API_USER_UPDATE:
                $this->saveUserLog();
                break;
            default:
                break;
        }

        \DB::disconnect();
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    private function getLog(string $key)
    {
        $value = $this->log->get($key);

        return $value ?? null;
    }

    /**
     * @return array
     */
    private function formatBaseLog(): array
    {
        $response = $this->getLog('response');

        $success = $response->success ?? null;
        $code = $response->error->code ?? null;

        return [
            'operator_id' => $this->getLog('operator_id'),
            'action'      => $this->getLog('action'),
            'date'        => $this->getLog('date'),
            'datetime'    => $this->getLog('datetime'),

            'success' => $success,
            'code'    => $code,

            'request_url'    => $this->getLog('request_url'),
            'request_params' => $this->getLog('request_params'),
            'response'       => $response,

            'ip'         => $this->getLog('ip'),
            'trace_code' => $this->getLog('trace_code'),
            'auth_token' => $this->getLog('auth_token'),
            'exception'  => $this->getLog('exception'),
        ];
    }

    /**
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    private function saveUserLog()
    {
        $attributes = $this->formatBaseLog();

        if (isset($attributes['request_params']['password'])) {
            $attributes['request_params']['password'] = '******';
        }

        resolve(LogApiUserRepository::class)->create($attributes);
    }
}
