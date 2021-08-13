<?php

namespace App\Repositories;

use App\Repositories\Traits\RepositoryExtendTrait;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class PlatformRepository
 * @package App\Repositories
 */
class PlatformRepository extends BaseRepository
{
    use RepositoryExtendTrait;

    public function model()
    {
        return '\\App\\Models\\Platform';
    }

    /**
     * @param array $where
     * @param array|string[] $columns
     * @return mixed
     */
    public function getPlatform(array $where, array $columns = ['*'])
    {
        return $this->firstWhereIn($where, $columns);
    }
}
