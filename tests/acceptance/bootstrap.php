<?php

use AcceptanceTests\Bootstrap;

//require_once '/var/www/lib/auto_prepend_file.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);


echo PHP_EOL . PHP_EOL . 'WEB_SERVER_HOST=' . getenv('WEB_SERVER_HOST') . PHP_EOL . PHP_EOL;

// @todo move to server start bash script. set env and use it later on.
define('WEB_SERVER_HOST', 'localhost');
define('WEB_SERVER_PORT', '8000');
define('WEB_SERVER_DOCROOT', __DIR__ . '/../../');


$params = array(
    'config_original' => __DIR__ . '/../../config/config.php',
    'config_test'     => __DIR__ . '/config.php',
    'app_db'          => __DIR__ . '/../../data/appdb.sqlite',
);

$bootstrap = new Bootstrap($params);
$bootstrap->init();