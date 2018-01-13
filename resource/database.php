<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$paths = ['source' => ROOT_PATH . 'src', 'metadata' => ROOT_PATH . 'metadata'];
$dbParams = [
    'driver' => getenv('DATABASE_MYSQL_DRIVE'),
    'user' => getenv('DATABASE_MYSQL_USER'),
    'password' => getenv('DATABASE_MYSQL_PASSWORD'),
    'dbname' => getenv('DATABASE_MYSQL_DB_NAME'),
    'host' => getenv('DATABASE_MYSQL_IP'),
    'port' => getenv('DATABASE_MYSQL_PORT'),
    'charset' => getenv('DATABASE_MYSQL_CHARSET')
];

$cache = new \Doctrine\Common\Cache\ArrayCache;

$config = new \Doctrine\ORM\Configuration();
$config->setMetadataCacheImpl($cache);

$driverImpl = $config->newDefaultAnnotationDriver(ROOT_PATH . "/src/Entity");
$config->setMetadataDriverImpl($driverImpl);

$config->setQueryCacheImpl($cache);
$config->setAutoGenerateProxyClasses(true);

$config->setProxyDir(ROOT_PATH  . "storage/doctrine-proxy");
$config->setProxyNamespace('Proxies');

$config->addCustomDatetimeFunction('date_format', 'Luxifer\DQL\Datetime\DateFormat');
$entityManager = EntityManager::create($dbParams, $config);
