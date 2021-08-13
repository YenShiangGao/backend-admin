<?php

namespace App\Operation;

/**
 * Class Role
 * @package App\Operation
 */
class Role extends Record
{
    protected string $category = 'control';
    protected string $project = 'control_personnel';
    protected string $subproject = 'control_personnel_role';

    protected array $allowedFields = [
        'name',
        'parent_id',
    ];

    protected array $renameFields = [
        'parent_id' => 'parent',
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
                return array_search($value, config('constants.role.status'));
            case 'parent_id':

                $role = $this->getRole($value);

                // 將id轉成name
                return blank($role) ? '' : $role->name;
            default:
                return $value ?? '';
        }
    }
}
