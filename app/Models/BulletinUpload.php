<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class BulletinUpload
 * @package App\Models
 */
class BulletinUpload extends Model
{
    protected $table = 'bulletin_upload';

    protected $fillable = [
        'bulletin_id',
        'original_name',
        'file',
        'operator_id',
    ];
}
