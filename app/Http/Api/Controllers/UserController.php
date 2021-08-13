<?php

namespace App\Http\Api\Controllers;

use Illuminate\Support\Arr;
use App\Exceptions\Api\ApiErrorException;
use App\Exceptions\Api\RoleNotExistException;
use App\Http\Api\Requests\UserDetailRequest;
use App\Http\Api\Requests\UserStoreRequest;
use App\Http\Api\Requests\UserListRequest;
use App\Http\Api\Requests\UserUpdateRequest;
use App\Http\Api\Resources\ErrorResource;
use App\Http\Api\Resources\SuccessResource;
use App\Http\Api\Resources\UserListRowResource;
use App\Http\Api\Resources\UserResource;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\UserService;

/**
 * Class UserController
 * @package App\Http\Api\Controllers
 */
class UserController extends Controller
{
    private $userRepository;
    private $roleRepository;
    private $userService;

    /**
     * UserController constructor.
     * @param UserRepository $userRepository
     * @param RoleRepository $roleRepository
     * @param UserService $userService
     */
    public function __construct(
        UserRepository $userRepository,
        RoleRepository $roleRepository,
        UserService $userService
    ) {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->userService = $userService;
    }

    /**
     * 建立使用者api
     *
     * @param UserStoreRequest $request
     * @return ErrorResource|SuccessResource
     */
    public function userStore(UserStoreRequest $request)
    {
        $params = [
            'account'  => $request->input('account'),
            'password' => $request->input('password'),
            'role_id'  => $request->input('role_id'),
            'status'   => $request->input('status'),
            'remark'   => $request->input('remark'),
        ];

        try {
            $role = $this->roleRepository->getRoleByIdOfAuth($params['role_id'], $withSelf = false);

            // 角色不存在 or 與登入者層級不符
            if (blank($role)) {
                throw new ApiErrorException('USER.STORE.ROLE_NOT_EXIST');
            }

            \DB::beginTransaction();

            $user = $this->userRepository->create([
                'account'  => $params['account'],
                'password' => $params['password'],
                'role_id'  => $params['role_id'],
                'status'   => Arr::get(config('constants.user.status'), $params['status']),
                'remark'   => $params['remark'],
            ]);

            $user->assignRole($role);

            \DB::commit();

            return new SuccessResource();
        } catch (ApiErrorException $e) {
            return new ErrorResource($e->getMessage(), $e);
        } catch (\Exception $e) {
            \DB::rollBack();

            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }

    /**
     * 使用者明細api
     *
     * @return ErrorResource|UserResource
     * @throws \Throwable
     */
    public function userDetail(UserDetailRequest $request)
    {
        $params = [
            'user_id' => $request->route('user_id'),
        ];

        try {
            $user = $this->userRepository->getUserByIdOfAuth($params['user_id']);
            if (blank($user)) {
                throw new ApiErrorException('USER.DETAIL.USER_NOT_EXIST');
            }

            return new UserResource($user);
        } catch (ApiErrorException $e) {
            return new ErrorResource($e->getMessage(), $e);
        } catch (\Exception $e) {
            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }

    /**
     * 使用者管理列表api
     *
     * @param UserListRequest $request
     * @return ErrorResource|UserListRowResource
     * @throws \Throwable
     */
    public function userList(UserListRequest $request)
    {
        $params = [
            'role_id' => $request->input('role_id'),
            'account' => $request->input('account'),
            'page'    => $request->input('page', 1),
            'limit'   => $request->input('limit', 15),
        ];

        try {
            $users = $this->userService->getUsersOfListCondition($params);
            $users->load('role', 'lastOperationRecord.operator', 'lastLoginRecord');

            $meta = [
                'limit'     => (int)$users->perPage(),
                'page'      => (int)$users->currentPage(),
                'last_page' => (int)$users->lastPage(),
                'total'     => (int)$users->total(),
            ];

            return UserListRowResource::collection($users->items())->addMeta($meta);
        } catch (RoleNotExistException $e) {
            return new ErrorResource('USER.LIST.ROLE_NOT_EXIST', $e);
        } catch (ApiErrorException $e) {
            return new ErrorResource($e->getMessage(), $e);
        } catch (\Exception $e) {
            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }

    /**
     * 更新使用者api
     *
     * @param UserUpdateRequest $request
     * @return ErrorResource|SuccessResource
     * @throws \Throwable
     */
    public function userUpdate(UserUpdateRequest $request)
    {
        $params = [
            'user_id'  => $request->route('user_id'),
            'role_id'  => $request->input('role_id'),
            'password' => $request->input('password') ?? '',
            // 允許輸入空值清空備註欄位
            'remark'   => $request->has('remark') ? $request->input('remark') ?? '' : null,
            'status'   => $request->input('status'),
        ];

        try {
            $user = $this->userRepository->getUserByIdOfAuth($params['user_id']);
            if (blank($user)) {
                throw new ApiErrorException('USER.UPDATE.USER_NOT_EXIST');
            }

            $conditions = $this->userService->fetchUpdateUserConditions($user, $params);

            if (filled($conditions)) {
                \DB::beginTransaction();
                $this->userService->updateUser($user, $conditions);
                \DB::commit();
            }

            return new SuccessResource();
        } catch (RoleNotExistException $e) {
            return new ErrorResource('USER.UPDATE.ROLE_NOT_EXIST', $e);
        } catch (ApiErrorException $e) {
            return new ErrorResource($e->getMessage(), $e);
        } catch (\Exception $e) {
            \DB::rollBack();

            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }
}
