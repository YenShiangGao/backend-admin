<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Models\Role;
use App\Models\User;

/**
 * Class BasePermissionSeeder
 */
class BasePermissionSeeder extends Seeder
{
    private string $adminRole;
    private string $adminAccount;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()['cache']->forget(config('permission.cache.key'));

        $this->setAdminUserAccount();
        $this->setAdminRoleName();

        auth()->setUser($this->getAdminUser());

        $this->command->alert('creating basic roles');
        $this->createRole();
        $this->command->line('');

        $this->command->alert('creating basic permissions');
        $this->createPermissions();
        $this->command->line('');

        $this->command->alert('setting role permissions');
        $this->givePermissionToAdminRole();
        $this->command->line('');

        $this->command->alert('setting administrator role');
        $this->giveAdminRoleToUser();
        $this->command->line('');
    }

    private function permissions()
    {
        return config('permissionName');
    }

    private function setAdminUserAccount()
    {
        $this->adminAccount = config('constants.user.account_admin');
    }

    private function setAdminRoleName()
    {
        $this->adminRole = config('constants.role.name_admin');
    }

    /**
     * 建立角色列表
     */
    private function createRole()
    {
        try {
            if (Role::where('name', $this->adminRole)->exists()) {
                $this->command->info("role `admin` already exists");
            } else {
                // 建立admin role
                Role::create([
                    'name'      => $this->adminRole,
                    'parent_id' => 0,
                    'depth'     => 0,
                    'sequence'  => 1,
                ]);

                $this->command->info("create `admin` role success");
            }
        } catch (\Exception $e) {
            $this->command->error('create role failed, ' . $e->getMessage());
            $this->command->error($e->getTraceAsString());
        }
    }

    /**
     * 建立權限列表
     */
    private function createPermissions()
    {
        try {
            $permissions = Permission::all()->pluck('name')->toArray();
            $deletePermission = array_diff($permissions, $this->permissions());
            collect($deletePermission)->each(function ($permissionName) {
                Permission::where('name', $permissionName)->delete();
                $this->command->error('permission `' . $permissionName . '` delete');
            });

            collect($this->permissions())->each(function ($permissionName) {
                if (Permission::where('name', $permissionName)->exists()) {
                    $this->command->line('permission `' . $permissionName . '` already exists');
                } else {

                    $remark = trans("permission.{$permissionName}");

                    Permission::create([
                        'name'   => $permissionName,
                        'remark' => $remark,
                    ]);
                    $this->command->info("create `{$permissionName}`({$remark}) permission success");
                }
            });
        } catch (\Exception $e) {
            $this->command->error('create permission failed!, ' . $e->getMessage());
        }
    }

    /**
     * 建立admin角色權限mapping
     */
    private function givePermissionToAdminRole()
    {
        try {
            $role = Role::findByName($this->adminRole);

            $role->givePermissionTo($this->permissions());

            $this->command->info('set `admin permission` success');
        } catch (\Exception $e) {
            $this->command->error('set `admin permission` failed, ' . $e->getMessage());
        }
    }

    /**
     * 設置管理員帳號角色權限
     */
    private function giveAdminRoleToUser()
    {
        try {
            $user = $this->getAdminUser();

            $role = Role::findByName($this->adminRole);

            if ($user->hasRole($role)) {
                $this->command->line("account:{$user->account} `role:admin` already exists");
            } else {
                // 指定權限
                $user->assignRole($role);

                // 更新user綁定的角色id
                $user->role_id = $role->id;
                $user->save();

                $this->command->info("give `role:admin` to account:{$user->account} success");
            }
        } catch (\Exception $e) {
            $this->command->error('give `role:admin` failed, ' . $e->getMessage());
            $this->command->error($e->getTraceAsString());
        }
    }

    private function getAdminUser()
    {
        return User::query()->where(['account' => $this->adminAccount])->first();
    }
}
