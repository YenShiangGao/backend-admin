<?php

namespace App\Http\Api\Resources\Traits;

use App\Http\Api\Resources\AnonymousResourceCollection;

/**
 * Trait SuccessResourceTrait
 * @package App\Http\Api\Resources\Traits
 */
trait SuccessResourceTrait
{
    use LogTrait;

    private string $logDriver = 'admin_api_info';

    /**
     * @param $request
     * @return array
     */
    public function with($request)
    {
        return [
            'success' => true,
        ];
    }

    public function toResponse($request)
    {
        $response = parent::toResponse($request);

        $this->saveLog($response);

        return $response;
    }

    public function addMeta(array $attributes = [])
    {
        return $this->additional([
            'meta' => $attributes,
        ]);
    }

    public static function collection($resource)
    {
        return tap(new AnonymousResourceCollection($resource, static::class), function ($collection) {
            if (property_exists(static::class, 'preserveKeys')) {
                $collection->preserveKeys = (new static([]))->preserveKeys === true;
            }
        });
    }
}
