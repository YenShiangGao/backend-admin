<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserLoginRecords
 * @package App\Models
 */
class UserLoginRecords extends Model
{
    protected $table = 'user_login_records';

    protected $guarded = ['id'];
}
