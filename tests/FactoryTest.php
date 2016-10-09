<?php
declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport;

use League\Container\Container;
use League\Event\Emitter;
use League\Event\EmitterInterface;
use React\EventLoop\Factory as LoopFactory;
use ApiClients\Foundation\Transport\Client;
use ApiClients\Foundation\Transport\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $container = new Container();
        $container->share(EmitterInterface::class, new Emitter());
        $loop = LoopFactory::create();
        $client = Factory::create(
            $container,
            $loop,
            []
        );
        $this->assertInstanceOf(Client::class, $client);
    }

    public function testCreateWithoutLoop()
    {
        $container = new Container();
        $container->share(EmitterInterface::class, new Emitter());
        $client = Factory::create(
            $container,
            null,
            []
        );
        $this->assertInstanceOf(Client::class, $client);
    }
}
