<?php

namespace App\Models;

use App\Models\Traits\OperationRecordTrait;
use App\Operation\Record as OperationRecord;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;
use Jiaxincui\ClosureTable\Traits\ClosureTable;
use App\Repositories\RoleClosureRepository;

/**
 * Class Role
 * @package App\Models
 */
class Role extends SpatieRole
{
    use OperationRecordTrait;

    // todo Closure功能擴充完畢後預計刪除
    use ClosureTable;

    protected $closureTable = 'role_closure';
    protected $parentColumn = 'parent_id';
    protected $ancestorColumn = 'ancestor_id';
    protected $descendantColumn = 'role_id';
    protected $distanceColumn = 'depth';

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public static function boot()
    {
        parent::boot();

        // 監看role model為created狀態時, 建立該role對應的體系closure
        static::created(function (Model $model) {
            resolve(RoleClosureRepository::class)->createClosure($model);

            $record = OperationRecord::get(class_basename($model));
            $record->setOperator(auth()->user())->setModel($model)->saveCreated();
        });

        static::updated(function (Model $model) {
            $record = OperationRecord::get(class_basename($model));
            $record->setOperator(auth()->user())->setModel($model)->saveUpdated();
        });

        static::deleted(function (Model $model) {
            $record = OperationRecord::get(class_basename($model));
            $record->setOperator(auth()->user())->setModel($model)->saveDeleted();
        });
    }

    public function normalUsers()
    {
        return $this->hasMany('App\Models\User', 'role_id')
            ->where('status', config('constants.user.status.enable'));
    }

    public function normalChildren()
    {
        return $this->hasMany('App\Models\Role', 'parent_id')
            ->where('status', config('constants.role.status.enable'));
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\Role', 'parent_id');
    }

    public function canDelete()
    {
        if (is_null($this->normal_children_count) || is_null($this->normal_users_count)) {
            $this->loadCount(['normalChildren', 'normalUsers']);
        }

        return !($this->normal_children_count > 0 || $this->normal_users_count > 0);
    }

    public function isAdmin()
    {
        return $this->name === config('constants.role.name_admin');
    }

    public function lastOperationRecord()
    {
        return $this->belongsTo('App\Models\OperationRecords', 'id' ,'model_id')
            ->where('model','Role')
            ->selectRaw('model_id,max(record_time) as last_record_at, operator_id')
            ->groupBy('model_id');
    }
}
