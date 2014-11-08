<?php

error_reporting(E_ALL);

date_default_timezone_set('UTC');

require 'vendor/autoload.php';


$configPath = __DIR__ . '/config';
$configData = include $configPath . '/config.php';
if (file_exists($configPath . '/config.local.php')) {
    $localConfig = include $configPath . '/config.local.php';
    $configData = array_replace_recursive($configData, $localConfig);
}

$config    = new Api\Module\Config();
$container = new Api\Module\Container();

try {
    $container
        ->setConfig($config->setConfig($configData))
        ->getModule()
        ->run();

} catch (\Exception $exc) {
    /** @var \Monolog\Logger $logger */
    $logger = $container->get(Api\Module\Container::LOGGER);
    $logger->addError($exc);

    header('HTTP/1.1 500 Internal Server Error');
}
