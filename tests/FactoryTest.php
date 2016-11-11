<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport;

use ApiClients\Foundation\Events\CommandLocatorEvent;
use DI\ContainerBuilder;
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
        $container = ContainerBuilder::buildDevContainer();
        $container->set(EmitterInterface::class, new Emitter());
        $loop = LoopFactory::create();
        $client = Factory::create(
            $container,
            $loop,
            []
        );
        $this->assertInstanceOf(Client::class, $client);
    }

    public function testCommandBusEvent()
    {
        $loop = LoopFactory::create();
        $emitter = new Emitter();
        $container = ContainerBuilder::buildDevContainer();
        $container->set(EmitterInterface::class, $emitter);
        Factory::create($container, $loop);

        $event = CommandLocatorEvent::create();
        $this->assertSame(0, count($event->getMap()));
        $emitter->emit($event);
        $this->assertSame(5, count($event->getMap()));
    }
}
