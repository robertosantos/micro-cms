<?php

define('ROOT_PATH',realpath(dirname(__DIR__)). DIRECTORY_SEPARATOR);

require(ROOT_PATH.'vendor/autoload.php');

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$dotenv = new Dotenv\Dotenv(ROOT_PATH);
$dotenv->load();

$paths = [ROOT_PATH."metadata/"];
$isDevMode = true;

$dbParams = [
    'driver' => getenv('DATABASE_MYSQL_DRIVE'),
    'user' => getenv('DATABASE_MYSQL_USER'),
    'password' => getenv('DATABASE_MYSQL_PASSWORD'),
    'dbname' => getenv('DATABASE_MYSQL_DB_NAME'),
    'host' => getenv('DATABASE_MYSQL_IP'),
    'port' => getenv('DATABASE_MYSQL_PORT'),
    'charset' => getenv('DATABASE_MYSQL_CHARSET')
];

$config = Setup::createXMLMetadataConfiguration($paths, $isDevMode);
$entityManager = EntityManager::create($dbParams, $config);

#Fix type missing
$platform = $entityManager->getConnection()->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('enum', 'string');
$platform->registerDoctrineTypeMapping('set', 'string');
