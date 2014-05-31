<?php

use AcceptanceTests\Bootstrap;

require_once '/var/www/lib/auto_prepend_file.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$params = array(
    'config_original' => __DIR__ . '/../../config/config.php',
    'app_db'          => __DIR__ . '/../../data/appdb.sqlite',
    'port'            => WEB_SERVER_PORT,
    'host'            => WEB_SERVER_HOST,
    'root'            => WEB_SERVER_DOCROOT,
    'ini'             => PHP_INI_FILE,
    'hhvm_port'       => HHVM_PORT,
);

$bootstrap = new Bootstrap($params);
$bootstrap->init();