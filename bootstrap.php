<?php

require_once __DIR__ . '/vendor/autoload.php';

ini_set('session.use_trans_sid', 0);
ini_set('session.use_only_cookies', 0);
ini_set('session.use_cookies', 0);

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

$cachedir = __DIR__ . '/cache';
$cachefile = $cachedir .'/container.php';

if (file_exists($cachefile)) {
    require_once $cachefile;
    $container = new ProjectServiceContainer();
} else {
    $container = new ContainerBuilder();

    $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/config'));
    $loader->load('parameters.yml');
    $loader->load('services.yml');

    if (!$container->getParameter('dev_mode')) {
        $container->compile();

        $dumper = new PhpDumper($container);

        if (!file_exists($cachedir)) {
            mkdir($cachedir, 0777, true);
        }
        file_put_contents($cachefile, $dumper->dump());
    }
}

if ($container->getParameter('dev_mode')) {
    ini_set('display_errors', 1);
} else {
    ini_set('display_errors', 0);
}
