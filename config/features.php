<?php

return [
    // 公告管理
    ['type' => 'category', 'code' => 'bulletin', 'parent' => ''],
    // 公告管理-主要公告
    ['type' => 'project', 'code' => 'bulletin_main', 'parent' => 'bulletin'],
    // 公告類型管理
    ['type' => 'project', 'code' => 'bulletin_type', 'parent' => 'bulletin'],

    // 站點管理
    ['type' => 'category', 'code' => 'site', 'parent' => ''],
    // 站點管理-遊戲商管理
    ['type' => 'project', 'code' => 'site_game', 'parent' => 'site'],

    // 總控
    ['type' => 'category', 'code' => 'control', 'parent' => ''],
    // 總控-人員管理
    ['type' => 'project', 'code' => 'control_personnel', 'parent' => 'control'],
    // 總控-人員管理-角色管理
    ['type' => 'subproject', 'code' => 'control_personnel_user', 'parent' => 'control_personnel'],
    // 總控-人員管理-使用者管理
    ['type' => 'subproject', 'code' => 'control_personnel_role', 'parent' => 'control_personnel'],
];
