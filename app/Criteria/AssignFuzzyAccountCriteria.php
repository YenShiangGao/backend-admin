<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AssignFuzzyAccountCriteria
 * @package App\Criteria
 */
class AssignFuzzyAccountCriteria implements CriteriaInterface
{
    private $account;

    /**
     * AssignFuzzyAccountCriteria constructor.
     * @param string|null $account
     */
    public function __construct(string $account = null)
    {
        $this->account = $account;
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
        return $model->where('account', 'like', "{$this->account}%");
    }
}
