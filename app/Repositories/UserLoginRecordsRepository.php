<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\Traits\RepositoryExtendTrait;

/**
 * Class UserLoginRecordsRepository
 * @package App\Repositories
 */
class UserLoginRecordsRepository extends BaseRepository
{
    use RepositoryExtendTrait;

    /**
     * @return string
     */
    public function model()
    {
        return '\\App\\Models\\UserLoginRecords';
    }
}
