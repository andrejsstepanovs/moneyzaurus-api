<?php

use Api\Module\Config;

return [
    Config::DEVMODE               => false,
    Config::PASSWORD_DEFAULT_COST => 4,
    Config::DATABASE  => [
        Config::DATABASE_ENTITIES   => [
            __DIR__ . '/../src/Entities'
        ],
        Config::DATABASE_CONNECTION => [
            'driver'   => 'pdo_sqlite',
            'path'     => __DIR__ . '/tmp.appdb.sqlite',
            'user'     => 'root',
            'password' => 'root',
            'memory'   => false
        ]
    ],
    Config::EMAIL => [
        'host'     => 'localhost',
        'port'     => 123,
        'security' => 'ssl',
        'username' => 'test@email.com',
        'password' => 'password',
        'test'     => true
    ],
    Config::LOG => [
        'file'  => __DIR__ . '/tmp.error.log'
    ],
    Config::SECURITY => [
        'max_login_attempts'     => 3,
        'login_abuse_sleep_time' => 2
    ]
];
