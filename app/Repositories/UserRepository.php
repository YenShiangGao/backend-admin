<?php

namespace App\Repositories;

use App\Criteria\AssignConditionsCriteria;
use App\Criteria\AssignFuzzyAccountCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\Traits\RepositoryExtendTrait;

/**
 * Class UserRepository
 * @package App\Repositories
 */
class UserRepository extends BaseRepository
{
    use RepositoryExtendTrait;

    /**
     * @return string
     */
    public function model()
    {
        return '\\App\\Models\\User';
    }

    /**
     * 取得該角色底下所有使用者
     *
     * @param int $ancestorId
     * @param bool $withSelf
     * @param string $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed
     */
    public function getDescendants(int $ancestorId, bool $withSelf = false, $columns = 'user.*')
    {
        $depthOperator = ($withSelf === true ? '>=' : '>');
        $where = [
            ['role_closure.ancestor_id', '=', $ancestorId],
            ['role_closure.depth', $depthOperator, 0],
        ];

        /**
         * ex.
         * select `user`.* from `user` inner join `role_closure` on `role_closure`.`role_id` = `user`.`role_id`
         * where `role_closure`.`ancestor_id` = 1 and `role_closure`.`depth` > 0
         */
        return $this->fetchJoinRoleClosureQuery()->findWhere($where, $columns);
    }

    /**
     * 分頁取得該角色底下所有使用者
     *
     * @param int $ancestorId
     * @param bool $withSelf
     * @param int $limit
     * @param string $columns
     * @return mixed
     */
    public function getDescendantsPaginate(
        int $ancestorId,
        bool $withSelf = false,
        int $limit = 15,
        $columns = ['user.*']
    ) {
        $depthOperator = ($withSelf === true ? '>=' : '>');
        $where = [
            ['role_closure.ancestor_id', '=', $ancestorId],
            ['role_closure.depth', $depthOperator, 0],
        ];

        return $this->fetchJoinRoleClosureQuery()->paginateWhere($where, $limit, $columns);
    }

    /**
     * @return UserRepository
     */
    public function fetchJoinRoleClosureQuery()
    {
        return $this->scopeQuery(function ($query) {
            return $query->join('role_closure', 'role_closure.role_id', '=', 'user.role_id');
        });
    }

    /**
     * 取得與登入者同體系使用者
     *
     * @param int $userId
     * @param bool $withSelf
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getUserByIdOfAuth(int $userId, bool $withSelf = false)
    {
        $ancestorId = auth()->user()->role_id;

        return $this->pushCriteria(new AssignConditionsCriteria(['user.id' => $userId]))
            ->getDescendants($ancestorId, $withSelf)
            ->first();
    }

    /**
     * @param array $where
     * @param array|string[] $columns
     * @return mixed
     */
    public function getUser(array $where, array $columns = ['*'])
    {
        return $this->firstWhereIn($where, $columns);
    }
}
