<?php

namespace App\Operation;

/**
 * Class User
 * @package App\Operation
 */
class User extends Record
{
    protected string $category = 'control';
    protected string $project = 'control_personnel';
    protected string $subproject = 'control_personnel_user';

    protected array $allowedFields = [
        'account',
        'password',
        'role_id',
        'status',
        'remark',
    ];

    protected array $securityFields = ['password'];

    protected array $renameFields = [
        'role_id' => 'role',
    ];

    /**
     * @param $field
     * @param $value
     * @return false|int|mixed|string
     */
    public function transferValue($field, $value)
    {
        switch ($field) {
            case 'status':
                // 將1轉成enable
                return array_search($value, config('constants.user.status'));
            case 'role_id':

                $role = $this->getRole($value);

                // 將id轉成name
                return blank($role) ? '' : $role->name;
            default:
                return $value ?? '';
        }
    }
}
