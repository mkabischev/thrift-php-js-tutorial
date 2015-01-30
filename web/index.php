<?php

include __DIR__.'/../vendor/autoload.php';

use Thrift\Protocol\TJSONProtocol;
use Thrift\TMultiplexedProcessor;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TPhpStream;
use tutorial\CalculatorIf;
use tutorial\CalculatorProcessor;
use tutorial\InvalidOperation;
use tutorial\Operation;
use tutorial\Work;

class CalculatorHandler implements CalculatorIf
{
    public function calculate(Work $w)
    {
        switch ($w->op) {
            case Operation::ADD: return $w->num1 + $w->num2;
            case Operation::DIVIDE: return $w->num1 / $w->num2;
            case Operation::MULTIPLY: return $w->num1 * $w->num2;
            case Operation::SUBTRACT: return $w->num1 - $w->num2;
        }
        throw new InvalidOperation([
            'what' => 1,
            'why' => sprintf('Unknown operation %s', Operation::$__names[$w->op])
        ]);
    }
}

$processor = new TMultiplexedProcessor();

$processor->registerProcessor('Calculator', new CalculatorProcessor(new CalculatorHandler()));

$transport = new TBufferedTransport(new TPhpStream(TPhpStream::MODE_R | TPhpStream::MODE_W));

$protocol = new TJSONProtocol($transport);

$transport->open();
$processor->process($protocol, $protocol);
$transport->close();
