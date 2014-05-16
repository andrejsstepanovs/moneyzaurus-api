<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';

$configData = include 'config/config.php';


$config    = new Api\Module\Config();
$container = new Api\Module\Container();

$container
    ->setConfig($config->setConfig($configData))
    ->getModule()
    ->run();