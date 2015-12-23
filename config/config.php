<?php

use Api\Module\Config;

$config = new Config();

return [
    Config::ROUTING               => 'Api\Module\Routing',
    Config::SLIM                  => 'Api\Slim',
    Config::DEVMODE               => true,
    Config::PASSWORD_DEFAULT_COST => 14, // 4
    Config::DATABASE  => [
        Config::DATABASE_ENTITIES   => [
            __DIR__ . '/../src/Entities',
        ],
        Config::DATABASE_CONNECTION => [
            'driver'   => 'pdo_mysql',
            'port'     => $config->env(['OPENSHIFT_MYSQL_DB_PORT', 'DB_PORT_3306_TCP_PORT'], 3306),
            'host'     => $config->env(['OPENSHIFT_MYSQL_DB_HOST', 'DB_PORT_3306_TCP_ADDR'], '127.0.0.1'),
            'user'     => $config->env(['OPENSHIFT_MYSQL_DB_USERNAME', 'DB_ENV_MYSQL_ROOT_USER'], 'root'),
            'password' => $config->env(['OPENSHIFT_MYSQL_DB_PASSWORD', 'DB_ENV_MYSQL_ROOT_PASSWORD'], 'root'),
            'dbname'   => $config->env(['OPENSHIFT_APP_NAME', 'DB_ENV_MYSQL_DATABASE'], 'app'),
            'charset'  => 'utf8',
        ],
    ],
    Config::EMAIL => [
        'host'     => $config->env('EMAIL_GATEWAY_HOST', 'localhost'),
        'port'     => $config->env('EMAIL_GATEWAY_PORT', 123),
        'security' => $config->env('EMAIL_GATEWAY_SECURITY', 'ssl'),
        'username' => $config->env('EMAIL_GATEWAY_USERNAME', 'test@email.com'),
        'password' => $config->env('EMAIL_GATEWAY_PASSWORD', 'password'),
        'test'     => (bool) $config->env('EMAIL_GATEWAY_TEST', true),
    ],
    Config::LOG => [
        //'file'  => __DIR__ . '/../error.log'
    ],
    Config::SECURITY => [
        'token_interval'         => 'P1Y', // PT10S
        'max_login_attempts'     => 10,    // 3
        'login_abuse_sleep_time' => 10,    // 4
    ]
];
