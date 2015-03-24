<?php

include __DIR__ . '/../bootstrap.php';

use Hellowords\Service\Synchronizer;
use Hellowords\Service\UserDictionaryStoreService;
use Hellowords\Service\UserStoreService;
use Hellowords\SynchronizerProcessor;
use Hellowords\UserDictionaryStoreProcessor;
use Hellowords\UserStoreProcessor;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use Thrift\Protocol\TJSONProtocol;
use Thrift\TMultiplexedProcessor;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TPhpStream;

$mt = microtime(true);

$logger = new Logger('hellowords');
$logger->pushHandler(new ErrorLogHandler());

// transport

$processor = new TMultiplexedProcessor();

$processor->registerProcessor(
    'UserStore',
    new UserStoreProcessor(new UserStoreService($entityManager, $logger))
);

$processor->registerProcessor(
    'Synchronizer',
    new SynchronizerProcessor(new Synchronizer($entityManager, $logger))
);

$processor->registerProcessor(
    'UserDictionaryStore',
    new UserDictionaryStoreProcessor(new UserDictionaryStoreService($entityManager, $logger))
);

$transport = new TBufferedTransport(new TPhpStream(TPhpStream::MODE_R | TPhpStream::MODE_W));

$protocol = new TJSONProtocol($transport);

$transport->open();
$processor->process($protocol, $protocol);
$transport->close();

$logger->debug(sprintf('Total time: %.5f', microtime(true) - $mt));