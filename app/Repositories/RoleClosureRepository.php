<?php

namespace App\Repositories;

use App\Models\Role;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\Traits\RepositoryExtendTrait;

/**
 * Class RoleClosureRepository
 * @package App\Repositories
 */
class RoleClosureRepository extends BaseRepository
{
    use RepositoryExtendTrait;

    /**
     * @return string
     */
    public function model()
    {
        return '\\App\\Models\\RoleClosure';
    }

    /**
     * 建立role closure
     *
     * @param Role $role
     * @return mixed
     */
    public function createClosure(Role $role)
    {
        $roleId = $role->id;
        $parentId = $role->parent_id;

        // ex. union (select 1, 1, 0)
        $unionQuery = \DB::query()->selectRaw(sprintf("%s, %s, 0", $roleId, $roleId));

        $selectQuery = $this->model
            ->selectRaw(sprintf("%s, ancestor_id, depth+1", $roleId))
            ->where('role_id', '=', $parentId)
            ->union($unionQuery);

        /**
         * ex.
         * insert into `role_closure` (`role_id`, `ancestor_id`, `depth`)
         * (select 1, ancestor_id, depth+1 from `role_closure` where `role_id` = 0)
         * union (select 1, 1, 0)
         */
        return $this->model->insertUsing(
            ['role_id', 'ancestor_id', 'depth'],
            $selectQuery
        );
    }
}
