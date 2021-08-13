<?php

namespace App\Models;

use App\Models\Traits\OperationRecordTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 * @package App\Models
 */
class User extends Authenticatable implements JWTSubject
{
    use OperationRecordTrait;

    use HasRoles;

    protected $table = 'user';
    protected $guarded = ['id'];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * @param $password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = \Crypt::encrypt($password);
    }

    public function getPasswordAttribute()
    {
        return \Crypt::decrypt($this->attributes['password']);
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role', 'role_id');
    }

    public function isDisable()
    {
        return (string)$this->status == config('constants.user.status.disable');
    }

    public function lastOperationRecord()
    {
        return $this->belongsTo('App\Models\OperationRecords', 'id' ,'model_id')
            ->where('model','User')
            ->selectRaw('model_id,max(record_time) as last_record_at, operator_id')
            ->groupBy('model_id');
    }

    public function lastLoginRecord()
    {
        return $this->belongsTo('App\Models\UserLoginRecords', 'id', 'user_id')
            ->selectRaw('user_id, max(login_at) as last_login_at')
            ->groupBy('user_id');
    }
}
