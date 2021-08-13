<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Repositories\UserRepository;

/**
 * Class CreateAdminSeeder
 * @package Database\Seeders
 */
class CreateAdminSeeder extends Seeder
{
    private string $password = 'qwe123';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $account = config('constants.user.account_admin');

        $userRepository = resolve(UserRepository::class);

        if ($userRepository->whereExists(['account' => $account])) {
            $this->command->info("帳號 {$account} 已存在");
        } else {
            $insert = [
                'account'    => $account,
                'password'   => \Crypt::encrypt($this->password),
                'role_id'    => 0,
                'status'     => config('constants.user.status.enable'),
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ];

            $userRepository->insert($insert);
            $this->command->info("帳號 {$account} 新增成功");
        }
    }
}
