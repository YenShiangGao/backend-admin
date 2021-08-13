<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\OperationRecordTrait;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Class Bulletin
 * @package App\Models
 */
class Bulletin extends Model
{
    use OperationRecordTrait;

    protected $table = 'bulletin';

    protected $fillable = [
        'type_id',
        'subject',
        'content',
        'operator_id',
    ];

    public function sends(): HasMany
    {
        return $this->hasMany('App\Models\BulletinSend', 'bulletin_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany('App\Models\BulletinUpload', 'bulletin_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo('App\Models\BulletinType', 'type_id');
    }

    public function platforms(): HasManyThrough
    {
        return $this->hasManyThrough(
            'App\Models\Platform',
            'App\Models\BulletinSend',
            'bulletin_id',
            'code',
            'id',
            'platform_code'
        );
    }

    public function isDisable()
    {
        return $this->status == config('constants.bulletin.status.disable');
    }
}
