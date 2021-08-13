<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\OperationRecordTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class BulletinType
 * @package App\Models
 */
class BulletinType extends Model
{
    use OperationRecordTrait;

    protected $table = 'bulletin_type';

    protected $fillable = ['name'];

    public function lastOperationRecord(): BelongsTo
    {
        return $this->belongsTo('App\Models\OperationRecords', 'id', 'model_id')
            ->where('model', 'BulletinType')
            ->selectRaw('model_id,max(record_time) as last_record_at, operator_id')
            ->groupBy('model_id');
    }
}
