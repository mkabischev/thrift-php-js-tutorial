<?php

namespace Thphp;

use Thrift\Exception\TApplicationException;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Protocol\TJSONProtocol;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TPhpStream;

class ServerApp
{
    protected $processor;

    protected $transport;

    public function __construct($processor)
    {
        $this->processor = $processor;
        $this->transport = $this->createTransport();
    }

    protected function createTransport()
    {
        return new TBufferedTransport(new TPhpStream(TPhpStream::MODE_R | TPhpStream::MODE_W));
    }

    protected function createProtocol($proto)
    {
        switch ($proto) {
            case 'application/x-thrift':
                return new TBinaryProtocol($this->transport, true, true);
            case 'application/json':
                return new TJSONProtocol($this->transport);
            default:
                throw new TApplicationException(sprintf('Unsupported protocol %s.', $proto));
        }
    }

    public function boot()
    {
        $this->transport->open();
    }

    public function handle($proto)
    {
        $protocol = $this->createProtocol($proto);
        header('Content-Type: ' .  $proto);
        $this->processor->process($protocol, $protocol);
    }

    public function terminate()
    {
        $this->transport->close();
    }
}