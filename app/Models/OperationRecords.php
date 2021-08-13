<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class OperationRecords
 * @package App\Models
 */
class OperationRecords extends Model
{
    protected $table = 'operation_records';
    protected $guarded = [];

    protected $casts = [
        'original' => 'array',
        'changes'  => 'array',
    ];

    public function operator()
    {
        return $this->belongsTo('App\Models\User', 'operator_id');
    }
}
