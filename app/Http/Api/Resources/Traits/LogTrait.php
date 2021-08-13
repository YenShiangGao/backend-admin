<?php

namespace App\Http\Api\Resources\Traits;

use App\Jobs\LogApiJob;
use App\Support\TraceInfo;

/**
 * Trait LogTrait
 * @package App\Http\Api\Resources\Traits
 */
trait LogTrait
{
    private $exception = null;

    // todo 加入黑/白名單確認是否寫入log
    private array $allow = [];
    private array $exclude = [];

    /**
     * @param $response
     */
    private function saveLog($response)
    {
        $isLogin = auth()->check();

        $route = request()->route();

        $requestParams = request()->all();
        if (request()->hasFile('file')) {
            $requestParams['file'] = request()->file('file')->getClientOriginalName();
        }

        $log = [
            'operator_id' => $isLogin ? auth()->user()->id : '',
            'action'      => filled($route) ? $route->getActionMethod() : '',
            'date'        => now()->toDateString(),
            'datetime'    => now()->toDateTimeString(),

            'http_method'    => request()->method(),
            'request_url'    => request()->url(),
            'request_params' => $requestParams,
            'response'       => $response->getData(),

            'ip'         => request()->ip(),
            'trace_code' => TraceInfo::getTraceCode(),
            'auth_token' => TraceInfo::getAuthToken(),

            'exception' => $this->formatException(),
        ];

        // todo 改用horizon queues寫入
        dispatch(new LogApiJob(collect($log)));

        // todo 開發階段先存實體log做debug
        logs($this->logDriver)->info(json_encode($log, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    /**
     * @return array
     */
    public function formatException(): array
    {
        if (blank($this->exception)) {
            return [];
        }

        $e = $this->exception;

        return [
            'exception' => get_class($e),
            'file'      => $e->getFile(),
            'line'      => $e->getLine(),
            'msg'       => $e->getMessage(),
        ];
    }
}
