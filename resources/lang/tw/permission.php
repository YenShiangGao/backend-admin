<?php

/**
 * 新增時須同步維護設定檔 config/permissionName.php
 */
return [
    // 開發設定
    'develop:reload'     => '開發設定-重整不登出',

    // 人員管理_使用者管理
    'user:view'          => '總控管理_人員管理_使用者管理_列表',
    'user:add'           => '總控管理_人員管理_使用者管理_新增',
    'user:edit'          => '總控管理_人員管理_使用者管理_修改',

    // 人員管理_角色管理
    'role:view'          => '總控管理_人員管理_角色管理_列表',
    'role:add'           => '總控管理_人員管理_角色管理_新增',
    'role:edit'          => '總控管理_人員管理_角色管理_修改',
    'role:delete'        => '總控管理_人員管理_角色管理_刪除',

    // 操作紀錄
    'records:view'       => '總控管理_操作紀錄',

    // 數據中心
    'report:view'        => '數據中心',

    // 公告管理
    'bulletin:view'      => '公告管理_列表',
    'bulletin:add'       => '公告管理_新增',
    'bulletin:edit'      => '公告管理_修改',
    'bulletin:type_view' => '公告管理_類型列表',
    'bulletin:type_add'  => '公告管理_類型新增',
    'bulletin:type_edit' => '公告管理_類型修改',

    // 站點管理
];
