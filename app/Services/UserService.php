<?php

namespace App\Services;

use App\Criteria\AssignConditionsCriteria;
use App\Criteria\AssignFuzzyAccountCriteria;
use App\Events\UserLogoutEvent;
use App\Exceptions\Api\RoleNotExistException;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;
use Illuminate\Support\Arr;

/**
 * Class UserService
 * @package App\Services
 */
class UserService
{
    private $userRepository;
    private $roleRepository;

    /**
     * UserService constructor.
     * @param UserRepository $userRepository
     * @param RoleRepository $roleRepository
     */
    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * 依`管理列表api`所帶入的條件, 取得使用者列表
     *
     * @param array $attributes
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getUsersOfListCondition(array $attributes)
    {
        if (filled($attributes['role_id'])) {
            $role = $this->roleRepository->getRoleByIdOfAuth($attributes['role_id']);

            // 角色不存在 or 與登入者層級不符
            if (blank($role)) {
                throw new RoleNotExistException();
            }

            // 顯示指定角色所綁定的會員
            $this->userRepository->pushCriteria(new AssignConditionsCriteria(['user.role_id' => $role->id]));
        } elseif (filled($attributes['account'])) {
            // 帳號模戶搜尋
            $this->userRepository->pushCriteria(new AssignFuzzyAccountCriteria($attributes['account']));
        }

        $ancestorId = auth()->user()->role->id;
        $withSelf = false;

        // 未指定任何條件, 則顯示登入者角色層級底下所有使用者
        return $this->userRepository
            ->orderBy('id', 'desc')
            ->getDescendantsPaginate($ancestorId, $withSelf, $attributes['limit']);
    }


    /**
     * 處里使用者更新資訊
     *
     * @param User $user
     * @param array $attributes
     * @return array
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function fetchUpdateUserConditions(User $user, array $attributes): array
    {
        $conditions = [];

        if (filled($attributes['role_id']) && $attributes['role_id'] != $user->role_id) {
            $role = $this->roleRepository->getRoleByIdOfAuth($attributes['role_id']);
            // 角色不存在 or 與登入者層級不符
            if (blank($role)) {
                throw new RoleNotExistException();
            }

            $conditions['role_id'] = $role->id;
        }

        if (filled($attributes['password']) && $attributes['password'] != $user->password) {
            $conditions['password'] = $attributes['password'];
        }

        // remark可清空, 允許空值
        if (isset($attributes['remark'])) {
            $conditions['remark'] = $attributes['remark'];
        }

        if (filled($attributes['status'])) {
            $conditions['status'] = Arr::get(config('constants.user.status'), $attributes['status']);
        }

        return $conditions;
    }

    /**
     * 更新使用者及綁定角色
     *
     * @param User $user
     * @param array $attributes
     */
    public function updateUser(User $user, array $attributes = [])
    {
        $needLogout = false;

        if (Arr::has($attributes, 'status') && $attributes['status'] == config('constants.user.status.disable')) {
            $needLogout = true;
        }

        if (Arr::has($attributes, 'role_id')) {
            $user->syncRoles($attributes['role_id']);
            $needLogout = true;
        }

        $user->update($attributes);

        if ($needLogout) {
            // 登出使用者
            event(new UserLogoutEvent($user));
        }
    }
}
