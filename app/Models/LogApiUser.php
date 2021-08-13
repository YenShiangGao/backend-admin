<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LogApiUser
 * @package App\Models
 */
class LogApiUser extends Model
{
    protected $connection = 'log_mariadb';

    protected $table = 'log_api_user';

    protected $guarded = [];

    protected $casts = [
        'request_params' => 'array',
        'response'       => 'array',
        'exception'      => 'array',
    ];
}
