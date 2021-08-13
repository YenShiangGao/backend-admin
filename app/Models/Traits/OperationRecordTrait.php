<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use App\Operation\Record as OperationRecord;

/**
 * Trait OperationRecordTrait
 * @package App\Models\Traits
 */
trait OperationRecordTrait
{
    public static function boot()
    {
        parent::boot();

        static::updated(function (Model $model) {
            $record = OperationRecord::get(class_basename($model));
            $record->setOperator(auth()->user())->setModel($model)->saveUpdated();
        });

        static::created(function (Model $model) {
            $record = OperationRecord::get(class_basename($model));
            $record->setOperator(auth()->user())->setModel($model)->saveCreated();
        });

        static::deleted(function (Model $model) {
            $record = OperationRecord::get(class_basename($model));
            $record->setOperator(auth()->user())->setModel($model)->saveDeleted();
        });
    }
}
