<?php

namespace App\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\Traits\RepositoryExtendTrait;

/**
 * Class BulletinRepository
 * @package App\Repositories
 */
class BulletinRepository extends BaseRepository
{
    use RepositoryExtendTrait;

    public function model()
    {
        return '\\App\\Models\\Bulletin';
    }

    /**
     * @param array $attributes
     * @param int $limit
     * @return mixed
     */
    public function fetchBulletinsPaginate(array $attributes, int $limit = 15)
    {
        $where = $this->getBulletinsConditions($attributes);

        return $this
            ->whereHasPlatformsQuery($attributes)
            ->orderBy('created_at', $attributes['sort_by'])
            ->paginateWhere($where, $limit);
    }

    /**
     * @param array $attributes
     * @return $this|BulletinRepository
     */
    private function whereHasPlatformsQuery(array $attributes)
    {
        if (empty($attributes['platform_code'])) {
            return $this;
        }

        /**
         * select * from `bulletin` where exists (
         *      select * from `bulletin_send` where `bulletin`.`id` = `bulletin_send`.`bulletin_id`
         *         and `platform_code` = 'fubo'
         *         and `type_id` = '3'
         *         and `bulletin_at` between '2021-06-07 00:00:00' and '2021-07-20 23:59:59'
         * )
         */
        return $this->whereHas('sends', function (Builder $query) use ($attributes) {
            $platformCode = $attributes['platform_code'];
            $typeId = Arr::get($attributes, 'type_id');
            $startAt = Arr::get($attributes, 'start_at');
            $endAt = Arr::get($attributes, 'end_at');

            $query->where('platform_code', $platformCode)
                ->when(filled($typeId), function (Builder $query) use ($typeId) {
                    return $query->where('type_id', $typeId);
                })
                ->when(filled($startAt) && filled($endAt), function (Builder $query) use ($startAt, $endAt) {
                    return $query->whereBetween('bulletin_at', [$startAt, $endAt]);
                });
        });
    }

    /**
     * @param array $attributes
     * @return array
     */
    private function getBulletinsConditions(array $attributes): array
    {
        $where = [];

        // 只顯示啟用得公告
        $where['status'] = config('constants.bulletin.status.enable');

        if (filled($attributes['start_at']) && filled($attributes['end_at'])) {
            $where[] = ['created_at', '>=', $attributes['start_at']];
            $where[] = ['created_at', '<=', $attributes['end_at']];
        }

        if (filled($attributes['type_id'])) {
            $where['type_id'] = $attributes['type_id'];
        }

        if ($attributes['subject']) {
            $where[] = ['subject', 'like', "{$attributes['subject']}%"];
        }

        return $where;
    }
}
