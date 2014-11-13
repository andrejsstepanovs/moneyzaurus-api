<?php
require_once 'vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

$paths = array('Entities');
$isDevMode = false;

// the connection configuration
$dbParams = array(
    'driver'   => 'pdo_mysql',
    'user'     => 'root',
    'password' => 'root',
    'host'     => 'moneyzaurus.local.com',
    'dbname'   => 'app',
);

// Any way to access the EntityManager from your application
$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
$entityManager = EntityManager::create($dbParams, $config);

$platform = $entityManager->getConnection()->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('enum', 'string');

return ConsoleRunner::createHelperSet($entityManager);

//$helperSet = new \Symfony\Component\Console\Helper\HelperSet(
//    array(
//        'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
//        'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
//    )
//);
