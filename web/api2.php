<?php

include __DIR__ . '/../bootstrap.php';

use Thphp\ServerApp;

$server = new ServerApp($container->get('thrift.processor'));

$server->boot();
$server->handle($_SERVER['HTTP_CONTENT_TYPE']);
$server->terminate();
