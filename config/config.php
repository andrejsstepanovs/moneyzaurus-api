<?php

use Api\Module\Config;


return [
    Config::ROUTING               => 'Api\Module\Routing',
    Config::SLIM                  => 'Api\Slim',
    Config::DEVMODE               => true,
    Config::PASSWORD_DEFAULT_COST => 14,
    Config::DATABASE  => [
        Config::DATABASE_ENTITIES   => [
            __DIR__ . '/../src/Entities'
        ],
        Config::DATABASE_CONNECTION => [
            'driver'   => 'pdo_mysql',
            'port'     => Config::env('DB_PORT_3306_TCP_PORT', 3306),
            'host'     => Config::env('DB_PORT_3306_TCP_ADDR', '127.0.0.1'),
            'user'     => Config::env('DB_ENV_MYSQL_ROOT_USER', 'root'),
            'password' => Config::env('DB_ENV_MYSQL_ROOT_PASSWORD', 'root'),
            'dbname'   => Config::env('DB_ENV_MYSQL_DATABASE', 'app'),
        ]
    ],
    Config::EMAIL => [
        'host'     => getenv('EMAIL_GATEWAY_HOST'),
        'port'     => getenv('EMAIL_GATEWAY_PORT'),
        'security' => getenv('EMAIL_GATEWAY_SECURITY'), // ssl
        'username' => getenv('EMAIL_GATEWAY_USERNAME'),
        'password' => getenv('EMAIL_GATEWAY_PASSWORD'),
        'test'     => !(bool)getenv('EMAIL_GATEWAY_TEST'),
    ],
    Config::LOG => [
        //'file'  => __DIR__ . '/../error.log'
    ],
    Config::SECURITY => [
        'token_interval'         => 'P1Y',
        'max_login_attempts'     => 10,
        'login_abuse_sleep_time' => 10
    ]
];
