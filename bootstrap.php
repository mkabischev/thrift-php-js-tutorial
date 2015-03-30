<?php

require_once __DIR__ . '/vendor/autoload.php';

ini_set('session.use_trans_sid', 0);
ini_set('session.use_only_cookies', 0);
ini_set('session.use_cookies', 0);

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

$container = new ContainerBuilder();

$loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/config'));
$loader->load('parameters.yml');
$loader->load('services.yml');
