<?php

namespace App\Operation\Traits;

use App\Repositories\RoleRepository;

/**
 * Trait RoleTrait
 * @package App\Operation\Traits
 */
trait RoleTrait
{
    protected $roles;

    /**
     * 取得角色資料
     *
     * @param $id
     * @return mixed
     */
    protected function getRole($id)
    {
        $roles = collect($this->roles);

        $role = $roles->firstWhere('id', $id);
        if (filled($role)) {
            return $role;
        }

        $role = resolve(RoleRepository::class)->getRole(['id' => $id], ['id', 'name']);

        $roles->push($role);

        $this->roles = $roles;

        return $role;
    }
}
