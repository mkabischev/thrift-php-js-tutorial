<?php

require_once __DIR__ . '/vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration([__DIR__ . '/src'], $isDevMode);

$conn = [
    'driver' => 'pdo_mysql',
    'host' => '10.0.1.2',
    'user' => 'root',
    'password' => 'test',
    'dbname' => 'test',
];

$entityManager = EntityManager::create($conn, $config);
