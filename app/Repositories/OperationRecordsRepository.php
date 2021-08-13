<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\Traits\RepositoryExtendTrait;

/**
 * Class OperationRecordsRepository
 * @package App\Repositories
 */
class OperationRecordsRepository extends BaseRepository
{
    use RepositoryExtendTrait;

    /**
     * @return string
     */
    public function model()
    {
        return '\\App\\Models\\OperationRecords';
    }

    /**
     * @param array $attributes
     * @param int $limit
     * @return mixed
     */
    public function fetchRecordsPaginate(array $where, int $limit = 15)
    {
        return $this->orderBy('record_time', 'desc')
            ->paginateWhere($where, $limit);
    }
}
