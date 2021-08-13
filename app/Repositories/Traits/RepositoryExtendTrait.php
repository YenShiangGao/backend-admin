<?php

namespace App\Repositories\Traits;

use Prettus\Repository\Events\RepositoryEntityDeleted;
use Prettus\Repository\Events\RepositoryEntityDeleting;

/**
 * Trait RepositoryExtendTrait
 * @package App\Traits
 */
trait RepositoryExtendTrait
{
    /**
     * @param array $where
     * @param null $limit
     * @param array|string[] $columns
     * @return mixed
     */
    public function paginateWhere(array $where, $limit = null, array $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();
        $this->applyWhereInConditions($where);

        $limit = is_null($limit) ? config('repository.pagination.limit', 15) : $limit;

        $results = $this->model->paginate($limit, $columns);
        $results->appends(app('request')->query());

        $this->resetModel();

        return $this->parserResult($results);
    }

    /**
     * @param array $where
     * @param array|string[] $columns
     * @return mixed
     */
    /**
     * @param array $where
     * @param array|string[] $columns
     * @return mixed
     */
    public function whereIn(array $where, array $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();
        $this->applyWhereInConditions($where);

        $model = $this->model->get($columns);
        $this->resetModel();

        return $this->parserResult($model);
    }

    /**
     * @param array $where
     * @return mixed
     */
    public function whereExists(array $where)
    {
        $this->applyCriteria();
        $this->applyScope();
        $this->applyWhereInConditions($where);

        $model = $this->model->exists();
        $this->resetModel();

        return $this->parserResult($model);
    }

    /**
     * @param array $where
     * @param array $data
     * @return mixed
     */
    public function updateWhere(array $where, array $data = [])
    {
        $this->applyCriteria();
        $this->applyScope();
        $this->applyWhereInConditions($where);

        $model = $this->model->update($data);
        $this->resetModel();

        return $this->parserResult($model);
    }

    /**
     * @param array $where
     * @return mixed
     */
    public function deleteWhereIn(array $where)
    {
        $this->applyScope();

        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);

        $this->applyWhereInConditions($where);

        event(new RepositoryEntityDeleting($this, $this->model->getModel()));

        $deleted = $this->model->delete();

        event(new RepositoryEntityDeleted($this, $this->model->getModel()));

        $this->skipPresenter($temporarySkipPresenter);
        $this->resetModel();

        return $deleted;
    }

    /**
     * @param array $values
     * @return mixed
     */
    public function insert(array $values)
    {
        return $this->model->insert($values);
    }

    /**
     * @param array $where
     * @return $this
     */
    protected function applyWhereInConditions(array $where)
    {
        $operators = ['>', '>=', '=', '<', '<=', '!=', 'like', '<>'];

        foreach ($where as $field => $value) {
            if (is_array($value) && count($value) == 3 && in_array($value[1], $operators)) {
                // 匹配 $where = [['user_id', '>=', 3], ['status', '!=', 'normal']]
                [$field, $condition, $val] = $value;
                $this->model = $this->model->where($field, $condition, $val);
            } else {
                if (is_array($value)) {
                    // 匹配 $where = ['user_id' => [1,2,3,4]]
                    $this->model = $this->model->whereIn($field, $value);
                } else {
                    // 匹配 $where = ['parent_id' => $id,  'status' => 'normal']
                    $this->model = $this->model->where($field, $value);
                }
            }
        }

        return $this;
    }

    /**
     * 切換到讀Master
     *
     * @return $this
     */
    public function onWriteConnection()
    {
        $this->model = $this->model::onWriteConnection();

        return $this;
    }

    /**
     * Wrapper result data
     *
     * @param mixed $result
     *
     * @return mixed
     */
    public function parserResult($result)
    {
        $result = parent::parserResult($result);

        $this->resetCriteria();
        $this->resetScope();

        return $result;
    }

    /**
     * @param array $where
     * @param array|string[] $columns
     * @return mixed
     */
    public function firstWhereIn(array $where, array $columns = ['*'])
    {
        return $this->whereIn($where, $columns)->first();
    }
}
