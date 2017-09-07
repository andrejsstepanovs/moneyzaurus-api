<?php

use AcceptanceTests\Bootstrap;

//require_once '/var/www/lib/auto_prepend_file.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);


$params = array(
    'config_original' => __DIR__ . '/../../config/config.php',
    'config_test'     => __DIR__ . '/config.php',
    'app_db'          => __DIR__ . '/../../data/appdb.sqlite',
);

$bootstrap = new Bootstrap($params);
$bootstrap->init();