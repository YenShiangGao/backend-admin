<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AssignConditionsCriteria
 * @package App\Criteria
 */
class AssignConditionsCriteria implements CriteriaInterface
{
    private $conditions;

    /**
     * AssignClosureRoleIdCriteria constructor.
     * @param null $roleId
     */
    public function __construct($conditions = null)
    {
        $this->conditions = $conditions;
    }

    /**
     * Apply criteria in query repository
     *
     * @param string $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $operators = ['>', '>=', '=', '<', '<=', '!=', 'like'];

        foreach ($this->conditions as $field => $value) {
            if (is_array($value) && count($value) == 3 && in_array($value[1], $operators)) {
                // 匹配 $where = [['user_id', '>=', 3], ['status', '!=', 'normal']]
                [$field, $condition, $val] = $value;
                $model = $model->where($field, $condition, $val);
            } else {
                if (is_array($value)) {
                    // 匹配 $where = ['user_id' => [1,2,3,4]]
                    $model = $model->whereIn($field, $value);
                } else {
                    // 匹配 $where = ['parent_id' => $id, 'status' => 'normal']
                    $model = $model->where($field, $value);
                }
            }
        }

        return $model;
    }
}
