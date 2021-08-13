<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Models\Bulletin;
use App\Models\BulletinUpload;
use App\Repositories\BulletinRepository;
use App\Repositories\BulletinSendRepository;
use App\Repositories\BulletinUploadRepository;
use App\Repositories\PlatformRepository;

/**
 * Class BulletinService
 * @package App\Services
 */
class BulletinService
{
    private $bulletinRepository;
    private $bulletinSendRepository;
    private $bulletinUploadRepository;
    private $platformRepository;

    /**
     * BulletinService constructor.
     */
    public function __construct(
        BulletinRepository $bulletinRepository,
        BulletinSendRepository $bulletinSendRepository,
        BulletinUploadRepository $bulletinUploadRepository,
        PlatformRepository $platformRepository
    ) {
        $this->bulletinRepository = $bulletinRepository;
        $this->bulletinSendRepository = $bulletinSendRepository;
        $this->bulletinUploadRepository = $bulletinUploadRepository;
        $this->platformRepository = $platformRepository;
    }

    /**
     * 新增公告
     *
     * @param array $attributes
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createBulletin(array $attributes)
    {
        $bulletin = $this->bulletinRepository->create([
            'type_id'     => $attributes['type_id'],
            'subject'     => $attributes['subject'],
            'content'     => $attributes['content'],
            'operator_id' => auth()->user()->id,
        ]);

        if (filled($attributes['attachments'])) {
             // 將附件與公告做id關聯
            $this->bindingAttachments($bulletin, $attributes['attachments']);
        }

        // 發送到平台管端
        $this->sendPlatforms($bulletin, $attributes['platforms']);
    }

    /**
     * 更新公告
     *
     * @param Bulletin $bulletin
     * @param array $attributes
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateBulletin(Bulletin $bulletin, array $attributes)
    {
        $bulletin->update([
            'type_id' => $attributes['type_id'],
            'subject' => $attributes['subject'],
            'content' => $attributes['content'],
        ]);

        // 更新附件
        $this->updateAttachments($bulletin, $attributes['attachments']);

        $platformCode = collect($bulletin->sends)->pluck('platform_code')->toArray();
        // 重新發送到平台管端
        $this->sendPlatforms($bulletin, $platformCode);
    }

    /**
     * 更新附件
     *
     * @param Bulletin $bulletin
     * @param array|null $attachmentsId
     */
    private function updateAttachments(Bulletin $bulletin, ?array $attachmentsId)
    {
        // 預計新增附件
        $new = $this->filterNewAttachmentsId($bulletin, $attachmentsId);
        if (filled($new)) {
            $this->bindingAttachments($bulletin, $new);
        }

        // 預計刪除附件
        $delete = $this->filterDeleteAttachmentsId($bulletin, $attachmentsId);
        if (filled($delete)) {
            collect($bulletin->attachments)
                ->whereIn('id', $delete)
                ->each(function ($attachment) {
                    $this->deleteAttachment($attachment);
                });
        }
    }

    /**
     * 刪除附件
     *
     * @param BulletinUpload $attachment
     */
    public function deleteAttachment(BulletinUpload $attachment)
    {
        $diskName = config('constants.filesystem.bulletin.file_disk');
        $file = config('constants.filesystem.bulletin.directory_name') . '/' . $attachment->file;

        $storage = Storage::disk($diskName);
        if ($storage->has($file)) {
            // 刪除實體檔案
            $storage->delete($file);
        }

        // 刪除db資料
        $attachment->delete();
    }

    /**
     * 檢查平台代號
     *
     * @param array $platformsCode
     * @return bool
     */
    public function checkPlatformsExist(array $platformsCode): bool
    {
        return collect($platformsCode)
                ->diff($this->platformRepository->all('code')->pluck('code'))
                ->count() === 0;
    }

    /**
     * 檢查附件id
     *
     * @param array $attachmentsId
     * @return bool
     */
    public function checkAttachmentsExist(array $attachmentsId): bool
    {
        $attachments = $this->bulletinUploadRepository->getAttachments([
            'id'          => $attachmentsId,
            'bulletin_id' => null,
        ]);

        // 附件id皆未綁定, 且數量與request一致
        return count($attachmentsId) === $attachments->count();
    }

    /**
     * 預計新增附件
     *
     * @param Bulletin $bulletin
     * @param array|null $attachments
     * @return array
     */
    public function filterNewAttachmentsId(Bulletin $bulletin, ?array $attachments): array
    {
        $originalAttachmentsId = collect($bulletin->attachments)->pluck('id');

        return collect($attachments)->diff($originalAttachmentsId)->values()->toArray();
    }

    /**
     * @param Bulletin $bulletin
     * @param array|null $attachments
     * @return array
     */
    public function filterDeleteAttachmentsId(Bulletin $bulletin, ?array $attachments): array
    {
        $originalAttachmentsId = collect($bulletin->attachments)->pluck('id');

        return $originalAttachmentsId->diff($attachments)->values()->toArray();
    }

    /**
     * 綁定公告附件
     *
     * @param Bulletin $bulletin
     * @param array $attachmentsId
     */
    private function bindingAttachments(Bulletin $bulletin, array $attachmentsId)
    {
        $where = [
            'id'          => $attachmentsId,
            'bulletin_id' => null,
        ];

        $this->bulletinUploadRepository->updateWhere($where, ['bulletin_id' => $bulletin->id]);
    }

    /**
     * 發送公告至平台端
     *
     * @param Bulletin $bulletin
     * @param array $platforms
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    private function sendPlatforms(Bulletin $bulletin, array $platforms)
    {
        $sends = collect($platforms)->map(function ($platformCode) use ($bulletin) {
            $attributes = [
                'bulletin_id'   => $bulletin->id,
                'platform_code' => $platformCode,
            ];

            $values = [
                'type_id'       => $bulletin->type_id,
                'bulletin_id'   => $bulletin->id,
                'bulletin_at'   => (string)$bulletin->created_at,
                'platform_code' => $platformCode,
                'status'        => config('constants.bulletin_send.status.init'),
            ];

            return $this->bulletinSendRepository->updateOrCreate($attributes, $values);
        });

        // todo 改成由排程發送公告給平台
        collect($sends)->each(function ($send) {
            // todo call platform api發送公告

            // todo 將status改成1(已發送)
        });
    }
}
