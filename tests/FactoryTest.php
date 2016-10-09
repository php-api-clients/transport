<?php
declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport;

use ApiClients\Foundation\Events\CommandLocatorEvent;
use League\Container\Container;
use League\Event\Emitter;
use League\Event\EmitterInterface;
use React\EventLoop\Factory as LoopFactory;
use ApiClients\Tools\TestUtilities\TestCase;
use ApiClients\Foundation\Transport\Client;
use ApiClients\Foundation\Transport\Factory;

class FactoryTest extends TestCase
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

    public function testCommandBusEvent()
    {
        $emitter = new Emitter();
        $container = new Container();
        $container->share(EmitterInterface::class, $emitter);
        Factory::create($container);

        $event = CommandLocatorEvent::create();
        $this->assertSame(0, count($event->getMap()));
        $emitter->emit($event);
        $this->assertSame(3, count($event->getMap()));
    }
}
