<?php

namespace App\Repositories;

use App\Repositories\Traits\RepositoryExtendTrait;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class BulletinUploadRepository
 * @package App\Repositories
 */
class BulletinUploadRepository extends BaseRepository
{
    use RepositoryExtendTrait;

    public function model()
    {
        return '\\App\\Models\\BulletinUpload';
    }

    public function getAttachments(array $where, array $columns = ['*'])
    {
        return $this->whereIn($where, $columns);
    }
}
