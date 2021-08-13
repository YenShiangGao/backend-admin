<?php

namespace App\Http\Api\Controllers;

use App\Http\Api\Requests\OperationListRequest;
use App\Http\Api\Requests\OperationFeatureRequest;
use App\Http\Api\Resources\OperationFeatureResource;
use App\Http\Api\Resources\OperationListResource;
use App\Http\Api\Resources\ErrorResource;
use App\Exceptions\Api\ApiErrorException;
use App\Repositories\UserRepository;
use App\Repositories\OperationRecordsRepository;
use App\Services\RecordService;
use Illuminate\Http\Request;

/**
 * Class RecordController
 * @package App\Http\Api\Controllers
 */
class RecordController extends Controller
{
    private $userRepository;
    private $operationRecordsRepository;
    private $recordService;

    public function __construct(
        UserRepository $userRepository,
        OperationRecordsRepository $operationRecordsRepository,
        RecordService $recordService
    ) {
        $this->userRepository = $userRepository;
        $this->operationRecordsRepository = $operationRecordsRepository;
        $this->recordService = $recordService;
    }

    /**
     * @param OperationListRequest $request
     * @return \App\Http\Api\Resources\OperationListResource|\App\Http\Api\Resources\ErrorResource
     */
    public function operationList(OperationListRequest $request)
    {
        $params = [
            'category'   => $request->input('category'),
            'project'    => $request->input('project'),
            'subproject' => $request->input('subproject'),
            'start_at'   => $request->input('start_at'),
            'end_at'     => $request->input('end_at'),
            'account'    => $request->input('account'),
            'page'       => $request->input('page', 1),
            'limit'      => $request->input('limit', 15),
        ];

        try {
            if (filled($params['account'])) {
                $operator = $this->userRepository->getUser(['account' => $params['account']]);
                if (blank($operator)) {
                    // 操作者不存在
                    throw new ApiErrorException('RECORD.OPERATION.ACCOUNT_NOT_EXIST');
                }

                $params['operator_id'] = $operator->id;
            }

            $conditions = $this->recordService->getOperationRecordsConditions($params);
            $records = $this->operationRecordsRepository->fetchRecordsPaginate($conditions, $params['limit']);

            $records->load(['operator']);

            $meta = [
                'limit'     => (int)$records->perPage(),
                'page'      => (int)$records->currentPage(),
                'last_page' => (int)$records->lastPage(),
                'total'     => (int)$records->total(),
            ];

            return OperationListResource::collection($records->items())->addMeta($meta);
        } catch (ApiErrorException $e) {
            return new ErrorResource($e->getMessage(), $e);
        } catch (\Exception $e) {
            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }

    /**
     * 功能列表(下拉選單)
     *
     * @param Request $request
     * @return \App\Http\Api\Resources\OperationFeatureResource
     */
    public function features(OperationFeatureRequest $request)
    {
        $params = [
            'code' => $request->input('code'),
        ];

        if (blank($params['code'])) {
            // 回傳第一層`類別`列表
            $features = collect(config('features'))->where('type', 'category')->values();
        } else {
            $features = collect(config('features'))->where('parent', $params['code'])->values();
        }

        return OperationFeatureResource::collection($features);
    }
}
