<?php

namespace App\Repositories;

use App\Repositories\Traits\RepositoryExtendTrait;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class BulletinSendRepository
 * @package App\Repositories
 */
class BulletinSendRepository extends BaseRepository
{
    use RepositoryExtendTrait;

    public function model()
    {
        return '\\App\\Models\\BulletinSend';
    }
}
