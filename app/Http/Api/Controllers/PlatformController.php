<?php

namespace App\Http\Api\Controllers;

use App\Exceptions\Api\ApiErrorException;
use App\Http\Api\Requests\PlatformStoreRequest;
use App\Http\Api\Requests\PlatformUpdateRequest;
use App\Http\Api\Resources\ErrorResource;
use App\Http\Api\Resources\PlatformResource;
use App\Http\Api\Resources\SuccessResource;
use App\Http\Api\Resources\PlatformListResource;
use App\Repositories\PlatformRepository;
use Illuminate\Support\Arr;

/**
 * Class PlatformController
 * @package App\Http\Api\Controllers
 */
class PlatformController
{
    private $platformRepository;

    /**
     * PlatformController constructor.
     */
    public function __construct(PlatformRepository $platformRepository)
    {
        $this->platformRepository = $platformRepository;
    }

    /**
     * 新增站別api
     *
     * @param PlatformStoreRequest $request
     * @return ErrorResource|SuccessResource
     */
    public function platformStore(PlatformStoreRequest $request)
    {
        $params = [
            'name' => $request->input('name'),
            'code' => $request->input('code'),
        ];

        try {
            $this->platformRepository->create([
                'name' => $params['name'],
                'code' => $params['code'],
            ]);

            return new SuccessResource();
        } catch (\Exception $e) {
            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }

    /**
     * 更新站別api
     *
     * @param PlatformUpdateRequest $request
     * @return ErrorResource|SuccessResource
     */
    public function platformUpdate(PlatformUpdateRequest $request)
    {
        $params = [
            'platform_code'      => $request->route('platform_code'),
            'currencies'         => $request->input('currencies'),
            'agent_site_status'  => $request->input('agent_site_status'),
            'member_site_status' => $request->input('member_site_status'),
        ];

        try {
            $platform = $this->platformRepository->getPlatform(['code' => $params['platform_code']]);
            if (blank($platform)) {
                throw new ApiErrorException('PLATFORM.UPDATE.PLATFORM_NOT_EXIST');
            }

            // 處理更新條件
            // todo 可移到service處理降低複雜度
            $conditions = [];

            if (filled($params['currencies'])) {
                $conditions['currency'] = $params['currencies'];
            }

            if (filled($params['agent_site_status'])) {
                $status = Arr::get(config("constants.platform.agent_site.status"), $params['agent_site_status']);
                $conditions['agent_site_status'] = $status;
            }

            if (filled($params['member_site_status'])) {
                $status = Arr::get(config("constants.platform.member_site.status"), $params['member_site_status']);
                $conditions['member_site_status'] = $status;
            }

            if (filled($conditions)) {
                $platform->update($conditions);
            }

            return new SuccessResource();
        } catch (ApiErrorException $e) {
            return new ErrorResource($e->getMessage(), $e);
        } catch (\Exception $e) {
            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }

    /**
     * 平台管理列表
     *
     * @return PlatformListResource|ErrorResource
     */
    public function platformList()
    {
        try {
            $platforms = $this->platformRepository->all();

            return PlatformListResource::collection($platforms);
        } catch (\Exception $e) {
            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }

    /**
     * 平台列表(下拉選單)
     * @return PlatformResource|ErrorResource
     */
    public function platforms()
    {
        try {
            $platforms = $this->platformRepository->all();

            return PlatformResource::collection($platforms);
        } catch (\Exception $e) {
            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }
}
