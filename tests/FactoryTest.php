<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport;

use ApiClients\Foundation\Events\CommandLocatorEvent;
use ApiClients\Foundation\Transport\Options;
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
        $loop = LoopFactory::create();
        $client = Factory::create(
            $container,
            $loop,
            [Options::USER_AGENT => 'u']
        );
        $this->assertInstanceOf(Client::class, $client);
    }
}
