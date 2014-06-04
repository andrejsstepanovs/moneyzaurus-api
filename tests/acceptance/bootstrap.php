<?php

use AcceptanceTests\Bootstrap;

//require_once '/var/www/lib/auto_prepend_file.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);


define('WEB_SERVER_HOST', 'localhost');
define('WEB_SERVER_PORT', '8000');
define('WEB_SERVER_DOCROOT', __DIR__ . '/../../');
define('HHVM_PORT', '8100');
define('PHP_INI_FILE', __DIR__ . '/php.ini');


$params = array(
    'config_original' => __DIR__ . '/../../config/config.php',
    'config_test'     => __DIR__ . '/config.php',
    'app_db'          => __DIR__ . '/../../data/appdb.sqlite',
    'port'            => WEB_SERVER_PORT,
    'host'            => WEB_SERVER_HOST,
    'root'            => WEB_SERVER_DOCROOT,
    'ini'             => PHP_INI_FILE,
    'hhvm_port'       => HHVM_PORT,
);

$bootstrap = new Bootstrap($params);
$bootstrap->init();