<?php

namespace App\Http\Api\Controllers;

use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use App\Events\UserLoginEvent;
use App\Events\UserLogoutEvent;
use App\Exceptions\Api\ApiErrorException;
use App\Http\Api\Resources\ErrorResource;
use App\Http\Api\Resources\LoginResource;
use App\Http\Api\Resources\SuccessResource;
use App\Http\Api\Resources\UserResource;
use App\Http\Api\Requests\LoginRequest;
use App\Http\Api\Requests\AuthPasswordUpdateRequest;

/**
 * Class AuthController
 * @package App\Http\Api\Controllers
 */
class AuthController extends Controller
{
    /**
     * 登入api
     *
     * @param LoginRequest $request
     * @return ErrorResource|LoginResource
     */
    public function login(LoginRequest $request)
    {
        $params = $request->only(['account', 'password']);

        try {
            if (!$token = auth()->attempt($params)) {
                // login failed
                return new ErrorResource('AUTH.LOGIN.AUTH_FAILED');
            }

            // 帳號已停用
            if (auth()->user()->isDisable()) {
                auth()->logout();

                return new ErrorResource('AUTH.LOGIN.USER_STATUS_DISABLE');
            }

            event(new UserLoginEvent(auth()->user(), $request));

            return new LoginResource(['token' => $token]);
        } catch (ApiErrorException $e) {
            return new ErrorResource($e->getMessage(), $e);
        } catch (\Exception $e) {
            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }

    /**
     * 登出api
     *
     * @return ErrorResource|SuccessResource
     */
    public function logout()
    {
        try {
            event(new UserLogoutEvent(auth()->user()));

            auth()->logout();

            return new SuccessResource();
        } catch (TokenExpiredException $e) {
            // token逾期
            return new SuccessResource();
        } catch (\Exception $e) {
            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }

    /**
     * 更新登入者密碼
     *
     * @param AuthPasswordUpdateRequest $request
     * @return ErrorResource|SuccessResource
     */
    public function passwordUpdate(AuthPasswordUpdateRequest $request)
    {
        $params = [
            'password'     => $request->input('password'),
            'old_password' => $request->input('old_password'),
        ];

        try {
            $user = auth()->user();

            // 新密碼不可與舊密碼相同
            if ($params['old_password'] == $params['password']) {
                return new ErrorResource('AUTH.PASSWORD.UPDATE.CANNOT_BE_THE_SAME');
            }

            // 驗證舊密碼是否正確
            if ($params['old_password'] !== $user->password) {
                return new ErrorResource('AUTH.PASSWORD.UPDATE.INVALID_PASSWORD');
            }

            $user->password = $params['password'];
            $user->save();

            return new SuccessResource();
        } catch (ApiErrorException $e) {
            return new ErrorResource($e->getMessage(), $e);
        } catch (\Exception $e) {
            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }

    /**
     * 登入者明細api
     *
     * @return ErrorResource|UserResource
     * @throws \Throwable
     */
    public function userDetail()
    {
        try {
            return new UserResource(auth()->user());
        } catch (\Exception $e) {
            return new ErrorResource('SYSTEM.FAILED', $e);
        }
    }
}
