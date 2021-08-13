<?php

namespace App\Repositories;

use App\Repositories\Traits\RepositoryExtendTrait;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class BulletinTypeRepository
 * @package App\Repositories
 */
class BulletinTypeRepository extends BaseRepository
{
    use RepositoryExtendTrait;

    public function model()
    {
        return '\\App\\Models\\BulletinType';
    }

    /**
     * @param string $name
     * @param int|null $ignoreTypeId
     * @return mixed
     */
    public function isNameExist(string $name, ?int $ignoreTypeId = null)
    {
        $where = ['name' => $name];

        if (filled($ignoreTypeId)) {
            $where[] = ['id', '<>', $ignoreTypeId];
        }

        return $this->whereExists($where);
    }
}
