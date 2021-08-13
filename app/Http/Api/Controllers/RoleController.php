<?php

namespace App\Http\Api\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Exceptions\Api\ApiErrorException;
use App\Http\Api\Requests\RoleDisableRequest;
use App\Http\Api\Requests\RoleStoreRequest;
use App\Http\Api\Requests\RoleUpdateRequest;
use App\Http\Api\Requests\RoleTreeRequest;
use App\Http\Api\Requests\RoleListRequest;
use App\Http\Api\Requests\RoleRequest;
use App\Http\Api\Resources\ErrorResource;
use App\Http\Api\Resources\RoleResource;
use App\Http\Api\Resources\SuccessResource;
use App\Http\Api\Resources\RoleListResource;
use App\Services\RoleService;
use App\Repositories\RoleRepository;

/**
 * Class RoleController
 * @package App\Http\Api\Controllers
 */
class RoleController extends Controller
{
    private $roleService;
    private $roleRepository;

    /**
     * RoleController constructor.
     *
     * @param RoleService $roleService
     * @param RoleRepository $roleRepository
     */
    public function __construct(RoleService $roleService, RoleRepository $roleRepository)
    {
        $this->roleService = $roleService;
        $this->roleRepository = $roleRepository;
    }

    /**
     * 新增角色api
     *
     * @param RoleStoreRequest $request
     * @return ErrorResource|SuccessResource
     * @throws \Throwable
     */
    public function roleStore(RoleStoreRequest $request)
    {
        $params = [
            'parent_id'   => $request->input('parent_role_id'),
            'name'        => $request->input('name'),
            'permissions' => $request->input('permissions'),
        ];

        try {
            $parentRole = $this->roleRepository->getRoleByIdOfAuth($params['parent_id'], $withSelf = true);
            if (blank($parentRole)) {
                // 上層角色不存在 or 與登入者層級不符
                throw new ApiErrorException('ROLE.STORE.PARENT_ROLE_NOT_EXIST');
            }

            // 檢查權限
            $permissions = $parentRole->getAllPermissions()->whereIn('id', $params['permissions'])->values();
            if ($permissions->count() !== count(array_unique($params['permissions']))) {
                throw new ApiErrorException('ROLE.STORE.PERMISSIONS_NOT_MATCH');
            }

            \DB::beginTransaction();

            // 新增角色
            $role = $this->roleService->createChildRole($parentRole, $params['name']);

            // 綁定角色權限
            $this->roleService->givePermissions($role, $permissions);

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
     * 角色樹狀結構api
     *
     * @return ErrorResource|SuccessResource
     * @throws \Throwable
     */
    public function roleTreeList(RoleTreeRequest $request)
    {
        $params = [
            'role_id' => $request->input('role_id'),
            'account' => $request->input('account'),
        ];

        try {
            if (blank($params['role_id'])) {
                $role = auth()->user()->role;
            } else {
                $role = $this->roleRepository->getRoleByIdOfAuth($params['role_id'], $withSelf = true);

                // 角色不存在 or 與登入者層級不符
                if (blank($role)) {
                    throw new ApiErrorException('ROLE.TREE.ROLE_NOT_EXIST');
                }
            }

            $data = $this->roleService->getDescendantsTree($role->id);

            return new SuccessResource($data);
        } catch (ApiErrorException $e) {
            return new ErrorResource($e->getMessage(), $e);
        } catch (\Exception $e) {
            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }

    /**
     * 角色管理列表api
     *
     * @param RoleListRequest $request
     * @return ErrorResource|RoleListResource
     */
    public function roleList(RoleListRequest $request)
    {
        $params = [
            'role_id' => $request->input('role_id'),
            'name'    => $request->input('name'),
            'page'    => $request->input('page', 1),
            'limit'   => $request->input('limit', 15),
        ];

        try {
            $role = $this->roleService->getRoleOfListCondition($params);

            // 角色不存在 or 與登入者層級不符
            if (blank($role)) {
                throw new ApiErrorException('ROLE.LIST.ROLE_NOT_EXIST');
            }

            if (filled($params['name'])) {
                $roles = collect()->push(
                    $role->loadCount(['normalChildren', 'normalUsers'])
                        ->load(['lastOperationRecord.operator'])
                );

                $targetRoleId = $role->parent_id;
            } else {
                $roles = $role->normalChildren
                    ->loadCount(['normalChildren', 'normalUsers'])
                    ->load(['lastOperationRecord.operator']);

                $targetRoleId = $role->id;
            }

            $records = new LengthAwarePaginator(
                $roles->forPage($params['page'], $params['limit']), // 將collect資料做分頁處里
                $roles->count(), // 總筆數
                $params['limit'], // 分頁筆數
                $params['page'] // 目前頁數
            );

            $meta = [
                'limit'     => (int)$records->perPage(),
                'page'      => (int)$records->currentPage(),
                'last_page' => (int)$records->lastPage(),
                'total'     => (int)$records->total(),
            ];

            return (new RoleListResource([
                'roles' => $records->items(),
                'level' => $this->roleService->getLevelRolesOfAuthAncestor($targetRoleId),
            ]))->addMeta($meta);

        } catch (ApiErrorException $e) {
            return new ErrorResource($e->getMessage(), $e);
        } catch (\Exception $e) {
            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }

    /**
     * 角色明細api
     *
     * @param RoleRequest $request
     * @return ErrorResource|RoleResource
     */
    public function roleDetail(RoleRequest $request)
    {
        $params = [
            'role_id' => $request->route('role_id'),
        ];

        try {
            if (blank($params['role_id'])) {
                $role = auth()->user()->role;
            } else {
                $role = $this->roleRepository->getRoleByIdOfAuth($params['role_id'], $withSelf = true);

                // 角色不存在 or 與登入者層級不符
                if (blank($role)) {
                    throw new ApiErrorException('ROLE.DETAIL.ROLE_NOT_EXIST');
                }
            }

            // 依照該角色在樹狀結構位置, 向上顯示整條線角色列表
            $role->level = $this->roleService->getLevelRolesOfAuthAncestor($role->id, $withAuth = false);

            return new RoleResource($role);
        } catch (ApiErrorException $e) {
            return new ErrorResource($e->getMessage(), $e);
        } catch (\Exception $e) {
            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }

    /**
     * 更新角色api
     *
     * @param RoleUpdateRequest $request
     * @return ErrorResource|SuccessResource
     * @throws \Throwable
     */
    public function roleUpdate(RoleUpdateRequest $request)
    {
        $params = [
            'role_id'     => $request->route('role_id'),
            'name'        => $request->input('name'),
            'permissions' => $request->input('permissions'),
        ];

        try {
            $role = $this->roleRepository->getRoleByIdOfAuth($params['role_id']);
            if (blank($role)) {
                // 角色不存在 or 與登入者層級不符
                throw new ApiErrorException('ROLE.UPDATE.ROLE_NOT_EXIST');
            }

            if ($role->name !== $params['name'] && $this->roleRepository->isNameExist($params['name'], $role->id)) {
                throw new ApiErrorException('ROLE.UPDATE.UNIQUE_NAME');
            }

            // 檢查 輸入權限 是否都包含在 上層啟已用權限 內
            $permissions = $role->parent->getAllPermissions()->whereIn('id', $params['permissions'])->values();
            if ($permissions->count() !== count(array_unique($params['permissions']))) {
                throw new ApiErrorException('ROLE.UPDATE.PERMISSIONS_NOT_MATCH');
            }

            \DB::beginTransaction();

            // 更新角色名稱
            $role->update(['name' => $params['name']]);

            // 更新權限(含下層連動)
            // todo 權限異動時, 受影響使用者須做登出操作
            $this->roleService->updatePermissions($role, $permissions);

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
     * 刪除角色api
     *
     * @param RoleDisableRequest $request
     * @return ErrorResource|SuccessResource
     * @throws \Throwable
     */
    public function roleDelete(RoleDisableRequest $request)
    {
        $params = [
            'role_id' => $request->route('role_id'),
        ];

        try {
            $role = $this->roleRepository->getRoleByIdOfAuth($params['role_id']);

            // 角色不存在 or 與登入者層級不符
            if (blank($role)) {
                throw new ApiErrorException('ROLE.DISABLE.ROLE_NOT_EXIST');
            }

            if (!$role->canDelete()) {
                throw new ApiErrorException('ROLE.DISABLE.CAN_NOT_DISABLE');
            }

            // 刪除角色
            $role->delete();

            return new SuccessResource();
        } catch (ApiErrorException $e) {
            return new ErrorResource($e->getMessage(), $e);
        } catch (\Exception $e) {
            \DB::rollBack();

            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }
}
