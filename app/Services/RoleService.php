<?php

namespace App\Services;

use App\Criteria\AllowRoleStatusCriteria;
use App\Models\Role;
use App\Repositories\RoleRepository;
use App\Repositories\RoleHasPermissionsRepository;
use Illuminate\Support\Collection;

/**
 * Class RoleService
 * @package App\Services
 */
class RoleService
{
    private $roleRepository;
    private $roleHasPermissionsRepository;

    /**
     * RoleService constructor.
     */
    public function __construct(
        RoleRepository $roleRepository,
        RoleHasPermissionsRepository $roleHasPermissionsRepository
    ) {
        $this->roleRepository = $roleRepository;
        $this->roleHasPermissionsRepository = $roleHasPermissionsRepository;
    }

    /**
     * 建立角色
     *
     * @param Role $parenRole
     * @param string $name
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createChildRole(Role $parenRole, string $name)
    {
        $lastSequence = $this->roleRepository->getLastSequence($parenRole->id);

        // 監看role model為created狀態時, 建立該role對應的體系closure
        return $this->roleRepository->create([
            'name'      => $name,
            'parent_id' => $parenRole->id,
            'depth'     => $parenRole->depth + 1,
            'sequence'  => $lastSequence + 1,
        ]);
    }

    /**
     * 檢查帶入的權限是否符合上層權限範圍
     *
     * @param array $parentPermissions
     * @param array $permissions
     * @return bool
     */
    public function hasNotExistPermissions(array $parentPermissions, array $permissions): bool
    {
        return collect($permissions)->diff($parentPermissions)->count() > 0;
    }

    /**
     * 綁定角色權限
     *
     * @param Role $role
     * @param $permissions
     */
    public function givePermissions(Role $role, $permissions)
    {
        $inserts = collect($permissions)
            ->map(function ($permission) use ($role) {
                return [
                    'permission_id' => $permission->id,
                    'role_id'       => $role->id,
                ];
            })
            ->toArray();

        $this->roleHasPermissionsRepository->insert($inserts);
    }

    /**
     * 取的下層角色樹狀結構
     *
     * @param int $ancestorId
     * @return array
     */
    public function getDescendantsTree(int $ancestorId): array
    {
        $data = $this->roleRepository
            ->withCount('normalUsers')
            ->pushCriteria(new AllowRoleStatusCriteria())
            ->getDescendants($ancestorId)
            ->map(function ($role) {
                return [
                    'id'                 => $role->id,
                    'name'               => $role->name,
                    'parent_id'          => $role->parent_id,
                    'normal_users_count' => $role->normal_users_count,
                ];
            })
            ->toArray();

        return $this->formatClosureTree($data);
    }

    /**
     * 將層級關係格式化成樹狀陣列
     *
     * @param array $data
     * @return array
     */
    private function formatClosureTree(array $data): array
    {
        $primary = 'id';
        $parent = 'parent_id';
        $children = 'children';

        if (!isset($data[0][$parent])) {
            return [];
        }

        $items = [];
        foreach ($data as $v) {
            $items[$v[$primary]] = $v;
        }

        $tree = [];
        foreach ($items as $item) {
            $parentId = $item[$parent];
            $primaryId = $item[$primary];

            if (isset($items[$parentId])) {
                $items[$item[$parent]][$children][] = &$items[$primaryId];
            } else {
                $tree[] = &$items[$primaryId];
            }
        }

        return $tree;
    }

    /**
     * 依登入者限制取得角色層級列表
     *
     * @param int $roleId
     * @param bool $withAuth
     * @return Collection
     */
    public function getLevelRolesOfAuthAncestor(int $roleId, bool $withAuth = true): Collection
    {
        $ancestors = $this->roleRepository->getAncestors($roleId, $withSelf = true);
        $authRoleDepth = $ancestors->firstWhere('id', auth()->user()->role->id)->depth;
        // 列表是否包含登入者角色(第一筆資料)
        $depthOperator = ($withAuth === true ? '>=' : '>');

        // 限制只回傳 `登入者` 可見層級
        return $ancestors->where('depth', $depthOperator, $authRoleDepth)->sortBy('depth')->values();
    }

    /**
     * 依`管理列表api`所帶入的條件, 取得角色
     *
     * @param array $attributes
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getRoleOfListCondition(array $attributes)
    {
        if (filled($attributes['role_id'])) {
            return $this->roleRepository->getRoleByIdOfAuth($attributes['role_id'], $withSelf = true);
        } elseif (filled($attributes['name'])) {
            return $this->roleRepository->getRoleByNameOfAuth($attributes['name'], $withSelf = false);
        }

        return auth()->user()->role;
    }

    /**
     * 更新權限
     *
     * @param Role $role
     * @param $permissions
     */
    public function updatePermissions(Role $role, $permissions)
    {
        // 角色目前權限
        $rolePermissionNames = $role->getPermissionNames();
        // 預計異動權限
        $permissionNames = collect($permissions)->pluck('name');
        // 上層權限
        $parentPermissions = $role->parent->getAllPermissions();

        // 預計啟用權限
        $enableNames = $permissionNames->diff($rolePermissionNames)->values();
        // 預計停用權限
        $disableNames = $rolePermissionNames->diff($permissionNames)->values();

        if ($enableNames->isNotEmpty()) {
            $enablePermissions = $parentPermissions->whereIn('name', $enableNames)->values();
            $this->givePermissions($role, $enablePermissions);
        }

        if ($disableNames->isNotEmpty()) {
            $disablePermissions = $parentPermissions->whereIn('name', $disableNames)->values();
            $this->revokeDescendantsWithSelfPermissions($role, $disablePermissions);
        }
    }

    /**
     * 移除自己及下層角色列表
     *
     * @param Role $role
     * @param $permissions
     */
    private function revokeDescendantsWithSelfPermissions(Role $role, $permissions)
    {
        // 取得自己及下層role_id列表
        $rolesId = $this->roleRepository->getDescendants($role->id, $withSelf = true, 'id')->pluck('id')->toArray();

        collect($permissions)
            ->each(function ($permission) use ($rolesId) {
                // 取得有該權限的role_id
                $deleteRolesId = $this->roleHasPermissionsRepository->findRoleHasPermissions(
                    $permission->id,
                    $rolesId
                )->pluck('role_id')->toArray();

                // todo 寫入操作紀錄

                // 刪除權限
                $this->roleHasPermissionsRepository->deleteRolesPermissions($permission->id, $deleteRolesId);
            });
    }
}
