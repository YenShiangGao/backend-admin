<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    echo 'admin v1 api, ' . now()->toDateTimeString();
});

Route::group(['middleware' => ['v1.guest']], function () {
    // 登入 api
    Route::post('login', 'AuthController@login');
});

Route::group(['middleware' => ['v1.auth']], function () {
    // 登出 api
    Route::post('logout', 'AuthController@logout');
    // 平台列表(下拉選單) api
    Route::get('platforms', 'PlatformController@platforms');

    // 修改登入者密碼
    Route::put('auth/password', 'AuthController@passwordUpdate');
    // 登入者詳細資料api
    Route::get('auth/user', 'AuthController@userDetail');

    // 人員管理-使用者
    Route::group(['prefix' => 'users'], function () {
        // 使用者管理列表
        Route::get('/', 'UserController@userList')->middleware('permission:user:view');
        // 建立使用者api
        Route::post('/', 'UserController@userStore')->middleware('permission:user:add');
        // 使用者明細
        Route::get('{user_id}', 'UserController@userDetail')->middleware('permission:user:view,user:edit');
        // 修改使用者
        Route::patch('{user_id}', 'UserController@userUpdate')->middleware('permission:user:edit');
    });

    // 人員管理-角色
    Route::group(['prefix' => 'roles'], function () {
        // 角色管理列表api
        Route::get('/', 'RoleController@roleList')->middleware('permission:role:view');
        // 角色樹狀結構api
        Route::get('tree', 'RoleController@roleTreeList');
        // 角色明細api
        Route::get('{role_id}', 'RoleController@roleDetail');
        // 建立角色api
        Route::post('/', 'RoleController@roleStore')->middleware('permission:role:add');
        // 修改角色api
        Route::put('{role_id}', 'RoleController@roleUpdate')->middleware('permission:role:edit');
        // 停用角色api
        Route::delete('{role_id}', 'RoleController@roleDelete')->middleware('permission:role:delete');
    });

    // 公告管理
    Route::group(['prefix' => 'bulletins'], function () {
        // 公告類型管理
        Route::group(['prefix' => 'types'], function () {
            // 建立公告類型api
            Route::post('/', 'BulletinController@typeStore')->middleware('permission:bulletin:type_add');
            // 修改公告類型api
            Route::put('{type_id}', 'BulletinController@typeUpdate')->middleware('permission:bulletin:type_edit');
            // 公告類型管理列表api
            Route::get('/', 'BulletinController@typeList')->middleware('permission:bulletin:type_view,bulletin:type_add');
        });

        // 公告列表api
        Route::get('/', 'BulletinController@bulletinList')->middleware('permission:bulletin:view');
        // 建立公告api
        Route::post('/', 'BulletinController@bulletinStore')->middleware('permission:bulletin:add');

        Route::group(['prefix' => '{bulletin_id}'], function () {
            // 公告詳細api
            Route::get('/', 'BulletinController@bulletinDetail')->middleware('permission:bulletin:view');
            // 修改公告api
            Route::put('/', 'BulletinController@bulletinUpdate')->middleware('permission:bulletin:edit');
            // 停用公告api
            Route::put('disable', 'BulletinController@bulletinDisable')->middleware('permission:bulletin:edit');
        });

        // 上傳公告附件api
        Route::post('file', 'BulletinController@bulletinFileUpload')->middleware('permission:bulletin:add,bulletin:edit');
    });

    // 程式管控
    Route::group(['prefix' => 'program', 'middleware' => ['permission']], function () {
        Route::group(['prefix' => 'platforms'], function () {
            // 新增站別api
            Route::post('/', 'PlatformController@platformStore');
            // 修改站別api
            Route::patch('{platform_code}', 'PlatformController@platformUpdate');
            // 站別列表api
            Route::get('/', 'PlatformController@platformList');
        });
    });

    // 紀錄
    Route::group(['prefix' => 'records'], function () {
        // 操作紀錄
        Route::group(['prefix' => 'operation', 'middleware' => ['permission:records:view']], function () {
            // 操作紀錄列表
            Route::get('/', 'RecordController@operationList');
            // 功能列表(下拉選單)
            Route::get('features', 'RecordController@features');
        });
    });
});
