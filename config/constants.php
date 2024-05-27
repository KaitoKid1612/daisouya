<?php

return [
    // 審査中のドライバーに許可されるパス
    'DRIVER_WAITING_ALLOW_PATH_LIST' => [
        ['path' => 'api/driver/test', 'pattern_type' => 1],
        ['path' => 'api/driver/token/destroy', 'pattern_type' => 1],
        ['path' => 'api/fcm-device-token/upsert', 'pattern_type' => 1],
        ['path' => 'api/fcm-device-token/show', 'pattern_type' => 3],
        ['path' => 'api/fcm-device-token/destroy', 'pattern_type' => 3],
        ['path' => 'api/driver/driver-task', 'pattern_type' => 1],
        ['path' => 'api/driver/user/show', 'pattern_type' => 3],
        ['path' => 'api/driver/user/allow-path/driver-waiting', 'pattern_type' => 1],

        ['path' => 'driver/dashboard', 'pattern_type' => 1],
        ['path' => 'driver/driver-task', 'pattern_type' => 1],
    ],

    // 正規表現で利用するパターン
    "MATCH_PATTERN_LIST" => [
        1 => ['name' => '完全一致'],
        2 => ['name' => '部分一致'],
        3 => ['name' => '前方一致'],
        4 => ['name' => '後方一致'],
    ],

    //システム価格表
    'SYSTEM_PRICE_LIST' => [
        ['id' => 1, 'value' => 3000],
        ['id' => 2, 'value' => 4000],
        ['id' => 3, 'value' => 5000],
        ['id' => 4, 'value' => 6000],
        ['id' => 5, 'value' => 7000],
        ['id' => 6, 'value' => 8000],
        ['id' => 7, 'value' => 9000],
    ],
];
