<?php
error_reporting(E_ALL);
ini_set('display_errors', (int)getenv('APP_DEV'));

require 'vendor/autoload.php';


$configFile = get_cfg_var('app_config_file');
$configFile = !$configFile ? 'config/config.php' : $configFile;

$configData = include __DIR__ . DIRECTORY_SEPARATOR . $configFile;


$config    = new Api\Module\Config();
$container = new Api\Module\Container();

$container
    ->setConfig($config->setConfig($configData))
    ->getModule()
    ->run();