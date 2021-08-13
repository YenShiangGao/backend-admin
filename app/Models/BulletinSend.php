<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class BulletinSend
 * @package App\Models
 */
class BulletinSend extends Model
{
    protected $table = 'bulletin_send';

    protected $fillable = [
        'type_id',
        'bulletin_id',
        'bulletin_at',
        'platform_code',
        'status',
    ];

    public function platform(): BelongsTo
    {
        return $this->belongsTo('App\Models\Platform', 'platform_code', 'code');
    }
}
