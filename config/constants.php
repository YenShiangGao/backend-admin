<?php

return [
    // 使用者
    'user' => [
        'account_admin' => 'admin',

        'status' => [
            'disable' => 0,
            'enable'  => 1,
        ],
    ],
    // 角色
    'role' => [
        'name_admin' => 'admin',

        'status' => [
            'disable' => 0,
            'enable'  => 1,
        ],
    ],
    // 公告
    'bulletin' => [
        'status' => [
            'disable' => 0,
            'enable'  => 1,
        ],
    ],
    // 公告發送
    'bulletin_send' => [
        'status' => [
            'init'    => 0, // 未處理
            'success' => 1,
            'failed'  => 2 // 失敗
        ],
    ],
    // 檔案上傳設置
    'filesystem' => [
        // 公告
        'bulletin' => [
            // 對應 config/filesystems.php disk設定
            'file_disk' => 'upload_file',
            'directory_name' => 'bulletin',
        ],
    ],
    // 站別
    'platform' => [
        // 管端狀態
        'agent_site'  => [
            'status' => [
                'disable' => 0,
                'enable'  => 1,
            ],
        ],
        // 客端狀態
        'member_site' => [
            'status' => [
                'disable' => 0,
                'enable'  => 1,
            ],
        ],
        'currency'    => [
            'status' => [
                'disable' => 0,
                'enable'  => 1,
            ],
        ],
    ],
    // 操作紀錄
    'operation_records' => [
        'action' => [
            'created' => 1,
            'updated' => 2,
            'deleted' => 3,
        ],
    ],
];
