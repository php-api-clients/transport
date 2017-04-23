<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport;

use ApiClients\Foundation\Events\CommandLocatorEvent;
use ApiClients\Foundation\Middleware\Locator\ContainerLocator;
use ApiClients\Foundation\Middleware\Locator\Locator;
use ApiClients\Foundation\Transport\Client;
use ApiClients\Foundation\Transport\Factory;
use ApiClients\Foundation\Transport\Options;
use ApiClients\Tools\TestUtilities\TestCase;
use DI\ContainerBuilder;
use League\Event\Emitter;
use League\Event\EmitterInterface;
use React\EventLoop\Factory as LoopFactory;

class FactoryTest extends TestCase
{
    public function testCreate()
    {
        $locator = $this->prophesize(Locator::class)->reveal();
        $loop = LoopFactory::create();
        $client = Factory::create(
            $locator,
            $loop
        );
        self::assertInstanceOf(Client::class, $client);
    }
}
