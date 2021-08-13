<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\Traits\RepositoryExtendTrait;

/**
 * Class RoleHasPermissionsRepository
 * @package App\Repositories
 */
class RoleHasPermissionsRepository extends BaseRepository
{
    use RepositoryExtendTrait;

    public function model()
    {
        return '\\App\\Models\\RoleHasPermissions';
    }

    /**
     * @param int $permissionId
     * @param array $rolesId
     * @return mixed
     */
    public function findRoleHasPermissions(int $permissionId, array $rolesId)
    {
        return $this->whereIn([
            'permission_id' => $permissionId,
            'role_id'       => $rolesId,
        ]);
    }

    /**
     * @param int $permissionId
     * @param array $rolesId
     * @return mixed
     */
    public function deleteRolesPermissions(int $permissionId, array $rolesId)
    {
        return $this->deleteWhereIn([
            'permission_id' => $permissionId,
            'role_id'       => $rolesId,
        ]);
    }
}
