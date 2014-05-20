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
            'driver'   => 'pdo_sqlite',
            'path'     => __DIR__ . '/tmp.appdb.sqlite',
            'user'     => 'root',
            'password' => 'root',
            'memory'   => false
        ]
    ],
    Config::EMAIL => [
        'host'     => 'smtp.gmail.com',
        'port'     => 465,
        'security' => 'ssl',
        'username' => 'email@gmail.com',
        'password' => 'password'
    ]
];
