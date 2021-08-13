<?php

namespace App\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use App\Http\Api\Resources\Traits\LogTrait;

/**
 * Class ErrorResource
 * @package App\Http\Api\Resources
 */
class ErrorResource extends JsonResource
{
    use LogTrait;

    private string $logDriver = 'admin_api_error';

    private $code;

    /**
     * ErrorResource constructor.
     * @param $resource
     * @param null $exception
     */
    public function __construct($resource, $exception = null)
    {
        parent::__construct($resource);

        self::wrap('error');

        if ($exception instanceof \Exception) {
            $this->exception = $exception;
        }

        $this->code = Arr::get(config('api.code'), $this->resource);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'code' => $this->code,
            'msg'  => '',
        ];
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function with($request)
    {
        return [
            'success' => false,
        ];
    }

    public function toResponse($request)
    {
        $response = parent::toResponse($request);

        $this->saveLog($response);

        $apiCode = config('api.code');

        switch ($this->code) {
            case $apiCode['SYSTEM.PERMISSION_DENIED']:
            case $apiCode['SYSTEM.NOT_LOGGED_IN']:
            case $apiCode['SYSTEM.API_NOT_EXIST']:
            case $apiCode['SYSTEM.FAILED']:
                return $response->setStatusCode($this->code);
        }

        return $response->setStatusCode($apiCode['SYSTEM.BAD_REQUEST']);
    }
}
