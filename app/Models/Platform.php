<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Platform
 * @package App\Models
 */
class Platform extends Model
{
    protected $table = 'platform';

    protected $fillable = [
        'name',
        'code',
        'currency',
        'agent_site_status',
        'member_site_status',
    ];

    protected $casts = [
        'currency' => 'array',
    ];
}
