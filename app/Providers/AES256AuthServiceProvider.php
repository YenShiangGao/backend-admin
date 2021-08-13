<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class AES256AuthServiceProvider
 * @package App\Providers
 */
class AES256AuthServiceProvider extends EloquentUserProvider
{
    /**
     * AES256AuthServiceProvider constructor.
     * @param $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $inputPassword = $credentials['password'];
        $authPassword = $user->getAuthPassword();

        try {
            return $authPassword == $inputPassword;
        } catch (\Exception $e) {
            return false;
        }
    }
}
