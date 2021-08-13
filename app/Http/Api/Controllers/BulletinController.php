<?php

namespace App\Http\Api\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use App\Exceptions\Api\ApiErrorException;
use App\Http\Api\Requests\BulletinRequest;
use App\Http\Api\Requests\BulletinStoreRequest;
use App\Http\Api\Requests\BulletinTypeStoreRequest;
use App\Http\Api\Requests\BulletinTypeUpdateRequest;
use App\Http\Api\Requests\BulletinFileUploadRequest;
use App\Http\Api\Requests\BulletinListRequest;
use App\Http\Api\Requests\BulletinUpdateRequest;
use App\Http\Api\Requests\BulletinDisableRequest;
use App\Http\Api\Resources\ErrorResource;
use App\Http\Api\Resources\SuccessResource;
use App\Http\Api\Resources\BulletinAttachmentResource;
use App\Http\Api\Resources\BulletinTypeListResource;
use App\Http\Api\Resources\BulletinResource;
use App\Http\Api\Resources\BulletinListResource;
use App\Repositories\BulletinRepository;
use App\Repositories\BulletinTypeRepository;
use App\Repositories\BulletinUploadRepository;
use App\Services\BulletinService;
use App\Support\Upload;

/**
 * Class BulletinController
 * @package App\Http\Api\Controllers
 */
class BulletinController extends Controller
{
    private $bulletinRepository;
    private $bulletinTypeRepository;
    private $bulletinUploadRepository;
    private $bulletinService;

    /**
     * BulletinController constructor.
     */
    public function __construct(
        BulletinRepository $bulletinRepository,
        BulletinTypeRepository $bulletinTypeRepository,
        BulletinUploadRepository $bulletinUploadRepository,
        BulletinService $bulletinService
    ) {
        $this->bulletinTypeRepository = $bulletinTypeRepository;
        $this->bulletinUploadRepository = $bulletinUploadRepository;
        $this->bulletinService = $bulletinService;
        $this->bulletinRepository = $bulletinRepository;
    }

    /**
     * 新增公告類型
     *
     * @param BulletinTypeStoreRequest $request
     * @return ErrorResource|SuccessResource
     */
    public function typeStore(BulletinTypeStoreRequest $request)
    {
        $params = [
            'name' => $request->input('name'),
        ];

        try {
            $this->bulletinTypeRepository->create([
                'name' => $params['name'],
            ]);

            return new SuccessResource();
        } catch (\Exception $e) {
            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }

    /**
     * 修改公告類型
     *
     * @param BulletinTypeUpdateRequest $request
     * @return ErrorResource|SuccessResource
     */
    public function typeUpdate(BulletinTypeUpdateRequest $request)
    {
        $params = [
            'type_id' => $request->route('type_id'),
            'name'    => $request->input('name'),
        ];

        try {
            $type = $this->bulletinTypeRepository->find($params['type_id']);
            if ($type->name === $params['name']) {
                return new SuccessResource();
            }

            if ($this->bulletinTypeRepository->isNameExist($params['name'], $type->id)) {
                throw new ApiErrorException('BULLETIN.TYPE.UPDATE.UNIQUE_NAME');
            }

            $type->update(['name' => $params['name']]);

            return new SuccessResource();
        } catch (ModelNotFoundException $e) {
            return new ErrorResource('BULLETIN.TYPE.UPDATE.TYPE_NOT_EXIST', $e);
        } catch (ApiErrorException $e) {
            return new ErrorResource($e->getMessage(), $e);
        } catch (\Exception $e) {
            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }

    /**
     * 公告類型管理列表
     *
     * @return BulletinTypeListResource|ErrorResource
     */
    public function typeList()
    {
        try {
            $list = $this->bulletinTypeRepository->all()->sortByDesc('updated_at')->values();

            $list->load(['lastOperationRecord.operator']);

            return BulletinTypeListResource::collection($list);
        } catch (\Exception $e) {
            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }

    /**
     * 新增公告api
     *
     * @param BulletinStoreRequest $request
     * @return ErrorResource|SuccessResource
     */
    public function bulletinStore(BulletinStoreRequest $request)
    {
        $params = [
            'platforms'   => $request->input('platforms'),
            'type_id'     => $request->input('type_id'),
            'subject'     => $request->input('subject'),
            'content'     => $request->input('content'),
            'attachments' => $request->input('attachments'),
        ];

        try {
            if (filled($params['platforms']) &&
                !$this->bulletinService->checkPlatformsExist($params['platforms'])
            ) {
                throw new ApiErrorException('BULLETIN.STORE.PLATFORMS_NOT_MATCH');
            }

            if (filled($params['attachments']) &&
                !$this->bulletinService->checkAttachmentsExist($params['attachments'])
            ) {
                throw new ApiErrorException('BULLETIN.STORE.ATTACHMENTS_NOT_MATCH');
            }

            $this->bulletinService->createBulletin($params);

            return new SuccessResource();
        } catch (ApiErrorException $e) {
            return new ErrorResource($e->getMessage(), $e);
        } catch (\Exception $e) {
            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }

    /**
     * 附件上傳api
     *
     * @param BulletinFileUploadRequest $request
     * @return BulletinAttachmentResource|ErrorResource
     */
    public function bulletinFileUpload(BulletinFileUploadRequest $request)
    {
        try {
            if (!$request->hasFile('file')) {
                throw new ApiErrorException('BULLETIN.FILE.UPLOAD.INVALID_FILE');
            }

            $file = $request->file('file');
            $upload = (new Upload('bulletin'))->setFile($file);
            $fileName = $upload->generateFileName();

            // 檔案上傳
            if (!$upload->storeFile($fileName)) {
                throw new ApiErrorException('BULLETIN.FILE.UPLOAD.UPLOAD_FAILED');
            }

            $attachment = $this->bulletinUploadRepository->create([
                'original_name' => $upload->getOriginalFileName(),
                'file'          => $fileName,
                'operator_id'   => auth()->user()->id,
            ]);

            return new BulletinAttachmentResource($attachment);
        } catch (ApiErrorException $e) {
            return new ErrorResource($e->getMessage(), $e);
        } catch (\Exception $e) {
            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }

    /**
     * 公告管理列表api
     *
     * @param BulletinListRequest $request
     * @return BulletinListResource|ErrorResource
     */
    public function bulletinList(BulletinListRequest $request)
    {
        $params = [
            'platform_code' => $request->input('platform_code'),
            'type_id'       => $request->input('type_id'),
            'start_at'      => $request->input('start_at'),
            'end_at'        => $request->input('end_at'),
            'subject'       => $request->input('subject'),
            'sort_by'       => $request->input('sort_by', 'desc'),
            'page'          => $request->input('page', 1),
            'limit'         => $request->input('limit', 15),
        ];

        try {
            if (filled($params['start_at']) && filled($params['end_at'])) {
                // 格式化Y-m-d時間為一日啟始, ex. 2021-07-01 -> 2021-07-01 00:00:00
                $params['start_at'] = Carbon::parse($params['start_at'])->startOfDay()->toDateTimeString();
                // 格式化Y-m-d時間為一日結束, ex. 2021-07-01 -> 2021-07-01 23:59:59
                $params['end_at'] = Carbon::parse($params['end_at'])->endOfDay()->toDateTimeString();
            }

            $bulletins = $this->bulletinRepository->fetchBulletinsPaginate($params, $params['limit']);

            $bulletins->load('type', 'attachments', 'platforms');

            $meta = [
                'limit'     => (int)$bulletins->perPage(),
                'page'      => (int)$bulletins->currentPage(),
                'last_page' => (int)$bulletins->lastPage(),
                'total'     => (int)$bulletins->total(),
            ];

            return BulletinListResource::collection($bulletins->items())->addMeta($meta);
        } catch (\Exception $e) {
            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }

    /**
     * 公告詳細資料api
     *
     * @param BulletinRequest $request
     * @return BulletinResource|ErrorResource
     */
    public function bulletinDetail(BulletinRequest $request)
    {
        $params = [
            'bulletin_id' => $request->route('bulletin_id'),
        ];

        try {
            $bulletin = $this->bulletinRepository->find($params['bulletin_id']);
            if ($bulletin->isDisable()) {
                throw new ApiErrorException('BULLETIN.DETAIL.BULLETIN_NOT_EXIST');
            }

            return new BulletinResource($bulletin);
        } catch (ModelNotFoundException $e) {
            return new ErrorResource('BULLETIN.DETAIL.BULLETIN_NOT_EXIST', $e);
        } catch (ApiErrorException $e) {
            return new ErrorResource($e->getMessage(), $e);
        } catch (\Exception $e) {
            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }

    /**
     * 更新公告api
     *
     * @param BulletinUpdateRequest $request
     * @return ErrorResource|SuccessResource
     */
    public function bulletinUpdate(BulletinUpdateRequest $request)
    {
        $params = [
            'bulletin_id' => $request->route('bulletin_id'),
            'type_id'     => $request->input('type_id'),
            'subject'     => $request->input('subject'),
            'content'     => $request->input('content'),
            'attachments' => $request->input('attachments'),
        ];

        try {
            $bulletin = $this->bulletinRepository->find($params['bulletin_id']);
            if ($bulletin->isDisable()) {
                throw new ApiErrorException('BULLETIN.UPDATE.BULLETIN_NOT_EXIST');
            }

            // 檢查類型是否存在
            if ($this->bulletinTypeRepository->all()->where('id', $params['type_id'])->isEmpty()) {
                throw new ApiErrorException('BULLETIN.UPDATE.TYPE_NOT_EXIST');
            }

            $newAttachments = $this->bulletinService->filterNewAttachmentsId($bulletin, $params['attachments']);
            if (filled($newAttachments) &&
                !$this->bulletinService->checkAttachmentsExist($newAttachments)) {
                throw new ApiErrorException('BULLETIN.UPDATE.ATTACHMENTS_NOT_MATCH');
            }

            $this->bulletinService->updateBulletin($bulletin, $params);

            return new SuccessResource();
        } catch (ModelNotFoundException $e) {
            return new ErrorResource('BULLETIN.UPDATE.BULLETIN_NOT_EXIST', $e);
        } catch (ApiErrorException $e) {
            return new ErrorResource($e->getMessage(), $e);
        } catch (\Exception $e) {
            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }

    /**
     * 停用公告api
     *
     * @param BulletinDisableRequest $request
     * @return ErrorResource|SuccessResource
     */
    public function bulletinDisable(BulletinDisableRequest $request)
    {
        $params = [
            'bulletin_id' => $request->route('bulletin_id'),
        ];

        try {
            $bulletin = $this->bulletinRepository->find($params['bulletin_id']);
            if ($bulletin->isDisable()) {
                throw new ApiErrorException('BULLETIN.DISABLE.BULLETIN_NOT_EXIST');
            }

            $bulletin->status = config('constants.bulletin.status.disable');
            $bulletin->save();

            // todo 發送停用異動到平台端

            return new SuccessResource();
        } catch (ModelNotFoundException $e) {
            return new ErrorResource('BULLETIN.DISABLE.BULLETIN_NOT_EXIST', $e);
        } catch (ApiErrorException $e) {
            return new ErrorResource($e->getMessage(), $e);
        } catch (\Exception $e) {
            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }
}
