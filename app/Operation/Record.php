<?php

namespace App\Operation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use App\Exceptions\Operation\OperationNotExistException;
use App\Repositories\OperationRecordsRepository;
use App\Operation\Traits\RoleTrait;

/**
 * Class Record
 * @package App\Operation
 */
abstract class Record
{
    use RoleTrait;

    protected $operationRecordsRepository;
    protected $name;
    protected $model;
    protected $operator;

    /**
     * 允許紀錄的欄位(白名單)
     * @var array
     */
    protected array $allowedFields = [];

    /**
     * 紀錄時需遮蔽的敏感資料欄位, ex:password
     * @var array
     */
    protected array $securityFields = [];

    /**
     * 輸出時須修改欄位名稱
     * @var array
     */
    protected array $renameFields = [];

    protected string $category;
    protected string $project;
    protected string $subproject;

    public function __construct(string $name)
    {
        $this->setName($name);

        $this->operationRecordsRepository = resolve(OperationRecordsRepository::class);
    }

    /**
     * @param string $name
     * @param bool $flush
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function get(string $name, bool $flush = false)
    {
        if (blank($name)) {
            throw new OperationNotExistException();
        }

        $abstract = '\\App\\Operation\\' . ucfirst($name);

        if (!app()->bound($abstract)) {
            try {
                $newInstance = resolve($abstract, ['name' => $name]);

                app()->instance($abstract, $newInstance);
            } catch (\ReflectionException $e) {
                throw new OperationNotExistException();
            }
        }

        $record = app()->make($abstract);

        return $flush ? $record->flush() : $record;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    protected function getName()
    {
        return $this->name;
    }

    /**
     * @param $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return Model
     */
    protected function getModel()
    {
        return $this->model;
    }

    /**
     * @param $operator
     * @return $this
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * @return mixed
     */
    protected function getOperator()
    {
        return $this->operator;
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function saveCreated()
    {
        $original = collect($this->getModel()->getAttributes());

        $data = [
            'action'   => config('constants.operation_records.action.created'),
            'original' => $this->filterAttributes($original->all()),
            'changes'  => null,
        ];

        return $this->createRecord($data);
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function saveUpdated()
    {
        $changes = collect($this->getModel()->getChanges());
        $original = collect($this->getModel()->getOriginal());

        $data = [
            'action'   => config('constants.operation_records.action.updated'),
            'original' => $this->filterAttributes($original->all()),
            'changes'  => $this->filterAttributes($changes->all()),
        ];

        return $this->createRecord($data);
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function saveDeleted()
    {
        $original = collect($this->getModel()->getAttributes());

        $data = [
            'action'   => config('constants.operation_records.action.deleted'),
            'original' => $this->filterAttributes($original->all()),
            'changes'  => null,
        ];

        return $this->createRecord($data);
    }

    /**
     * @param array $data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    protected function createRecord(array $data)
    {
        // todo 可改由job寫log
        return $this->operationRecordsRepository->create(array_merge($data, $this->getInitRecord()));
    }

    /**
     * 過濾紀錄欄位
     *
     * @param array $attributes
     * @return array
     */
    private function filterAttributes(array $attributes): array
    {
        $attributes = Arr::only($attributes, $this->allowedFields);

        collect($this->securityFields)->each(function ($field) use (&$attributes) {
            if (Arr::has($attributes, $field)) {
                Arr::set($attributes, $field, '******');
            }
        });

        return $attributes;
    }

    /**
     * @return array
     */
    protected function getInitRecord(): array
    {
        $now = now();

        return [
            'record_date' => $now->toDateString(),
            'record_time' => $now->toDateTimeString(),

            'operator_id'    => $this->getOperator()->id,
            'operator_model' => class_basename($this->getOperator()),

            'category'   => $this->category,
            'project'    => $this->project,
            'subproject' => $this->subproject,

            'model'    => class_basename($this->getModel()),
            'model_id' => $this->getModel()->id,
            'ip'       => request()->ip(),
        ];
    }

    /**
     * 格式化異動內容
     *
     * @param $record
     * @return array
     */
    public function formatChangeContent($record): array
    {
        $result = [];

        collect($this->allowedFields)
            ->intersect(array_keys($record->original))
            ->values()
            ->each(function ($field) use ($record, &$result) {
                // 轉換輸出欄位名稱, ex. role_id 轉換成 role
                $outputFiled = collect($this->renameFields)->get($field, $field);
                $originalValue = Arr::get($record->original, $field);
                $changeValue = Arr::get($record->changes, $field);

                if (blank($changeValue)) {
                    if (in_array($field, $this->securityFields)) {
                        return;
                    }

                    $result[$outputFiled] = $this->transferValue($field, $originalValue);

                    return;
                }

                // 欄位異動資訊, 格式[修改前, 修改後]
                // ex. role => ['角色1', 角色2]
                $result[$outputFiled] = [
                    $this->transferValue($field, $originalValue),
                    $this->transferValue($field, $changeValue),
                ];
            });

        return $result;

    }

    /**
     * 數值轉換, 在各子類別實作
     *
     * @param $field
     * @param $value
     * @return mixed
     */
    protected function transferValue($field, $value)
    {
        return $value ?? '';
    }
}
