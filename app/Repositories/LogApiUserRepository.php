<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\Traits\RepositoryExtendTrait;

/**
 * Class LogApiUserRepository
 * @package App\Repositories
 */
class LogApiUserRepository extends BaseRepository
{
    use RepositoryExtendTrait;

    public function model()
    {
        return '\\App\\Models\\LogApiUser';
    }
}
