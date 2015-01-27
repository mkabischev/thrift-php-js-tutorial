<?php

include __DIR__.'/../vendor/autoload.php';

use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TPhpStream;
use Thrift\Protocol\TJSONProtocol;
use tutorial\Operation;
use tutorial\CalculatorIf;
use tutorial\CalculatorProcessor;

class CalculatorImpl implements CalculatorIf
{
    public function calculate(\tutorial\Work $w)
    {
        switch ($w->op) {
            case Operation::ADD: return $w->num1 + $w->num2;
            case Operation::DIVIDE: return $w->num1 / $w->num2;
            case Operation::MULTIPLY: return $w->num1 * $w->num2;
            case Operation::SUBTRACT: return $w->num1 - $w->num2;
        }
        throw new tutorial\InvalidOperation([
            'what' => 1,
            'why' => sprintf('Unknown operation %s', Operation::$__names[$w->op])
        ]);
    }
}

$handler = new CalculatorImpl();
$processor = new CalculatorProcessor($handler);

$transport = new TBufferedTransport(new TPhpStream(TPhpStream::MODE_R | TPhpStream::MODE_W));

$protocol = new TJSONProtocol($transport);

$transport->open();
$processor->process($protocol, $protocol);
$transport->close();
