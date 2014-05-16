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
            'host'     => 'moneyzaurus.local.com',
            'user'     => 'root',
            'password' => 'root',
            'dbname'   => 'app',
            'charset'  => 'utf8'
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