<?php

include __DIR__ . '/bootstrap.php';

use Hellowords\UserStoreClient;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Protocol\TMultiplexedProtocol;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TCurlClient;

$mt = microtime(true);

$logger = new Logger('hellowords');
$logger->pushHandler(new ErrorLogHandler());

$socket = new TCurlClient('localhost', 9080, '/api2.php');

$transport = new TBufferedTransport($socket);
$protocol = new TBinaryProtocol($transport);

$client = new UserStoreClient(new TMultiplexedProtocol($protocol, 'UserStore'));

$transport->open();

$authResult = $client->authenticate(uniqid('x'), 'x');

var_dump($authResult);

$transport->close();

$logger->debug(sprintf('Total time: %.5f', microtime(true) - $mt));

