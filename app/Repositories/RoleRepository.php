<?php

namespace App\Repositories;

use App\Criteria\AllowRoleStatusCriteria;
use App\Criteria\AssignConditionsCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\Traits\RepositoryExtendTrait;

/**
 * Class RoleRepository
 * @package App\Repositories
 */
class RoleRepository extends BaseRepository
{
    use RepositoryExtendTrait;

    /**
     * @return string
     */
    public function model()
    {
        return '\\App\\Models\\Role';
    }

    /**
     * 取得同一層級 last sequence
     *
     * @param int $parentId
     * @return mixed
     */
    public function getLastSequence(int $parentId)
    {
        return $this->model->where('parent_id', $parentId)->max('sequence');
    }

    /**
     * 向下取得下層角色列表
     *
     * @param int $ancestorId
     * @param bool $withSelf
     * @param string $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed
     */
    public function getDescendants(int $ancestorId, bool $withSelf = false, $columns = 'roles.*')
    {
        $depthOperator = ($withSelf === true ? '>=' : '>');
        $where = [
            ['role_closure.ancestor_id', '=', $ancestorId],
            ['role_closure.depth', $depthOperator, 0],
        ];

        /**
         * ex.
         * select `roles`.* from `roles` inner join `role_closure` on `role_closure`.`role_id` = `roles`.`id`
         * where `role_closure`.`ancestor_id` = 1 and `role_closure`.`depth` >= 0
         */
        return $this->scopeQuery(function ($query) {
            return $query->join('role_closure', 'role_closure.role_id', '=', 'roles.id');
        })->findWhere($where, $columns);
    }

    /**
     * 向上取得上層列表
     *
     * @param int $roleId
     * @param bool $withSelf
     * @param string $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed
     */
    public function getAncestors(int $roleId, bool $withSelf = false, $columns = 'roles.*')
    {
        $depthOperator = ($withSelf === true ? '>=' : '>');
        $where = [
            ['role_closure.role_id', '=', $roleId],
            ['role_closure.depth', $depthOperator, 0],
        ];

        /**
         * ex.
         * select `roles`.* from `roles` inner join `role_closure` on `role_closure`.`role_id` = `roles`.`id`
         * where `role_closure`.`role_id` = '27' and `role_closure`.`depth` >= 0
         */
        return $this->scopeQuery(function ($query) {
            return $query->join('role_closure', 'role_closure.ancestor_id', '=', 'roles.id');
        })->findWhere($where, $columns);
    }

    /**
     * @param array $where
     * @param string[] $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed
     */
    public function getRoles(array $where = [], $columns = ['*'])
    {
        return $this->findWhere($where, $columns);
    }

    /**
     * @param array $where
     * @param string[] $columns
     * @return mixed
     */
    public function getRole(array $where = [], $columns = ['*'])
    {
        return $this->firstWhereIn($where, $columns);
    }

    /**
     * @param string $name
     * @param int|null $ignoreRoleId
     * @return mixed
     */
    public function isNameExist(string $name, ?int $ignoreRoleId = null)
    {
        $where = ['name' => $name];

        if (filled($ignoreRoleId)) {
            $where[] = ['id', '<>', $ignoreRoleId];
        }

        return $this->whereExists($where);
    }

    /**
     * @param int $roleId
     * @param bool $withSelf
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getRoleByIdOfAuth(int $roleId, bool $withSelf = false)
    {
        $ancestorId = auth()->user()->role_id;

        return $this->pushCriteria(new AllowRoleStatusCriteria())
            ->pushCriteria(new AssignConditionsCriteria(['roles.id' => $roleId]))
            ->getDescendants($ancestorId, $withSelf)
            ->first();
    }

    /**
     * @param string $name
     * @param bool $withSelf
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getRoleByNameOfAuth(string $name, bool $withSelf = false)
    {
        $ancestorId = auth()->user()->role_id;

        return $this->pushCriteria(new AllowRoleStatusCriteria())
            ->pushCriteria(new AssignConditionsCriteria(['roles.name' => $name]))
            ->getDescendants($ancestorId, $withSelf)
            ->first();
    }
}
