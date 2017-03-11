<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport\CommandBus\Handler;

use ApiClients\Foundation\Transport\CommandBus\Command\JsonEncodeCommand;
use ApiClients\Foundation\Transport\CommandBus\Handler\JsonEncodeHandler;
use ApiClients\Foundation\Transport\Service\JsonEncodeService;
use ApiClients\Tools\TestUtilities\TestCase;
use React\EventLoop\Factory;
use function Clue\React\Block\await;

class JsonEncodeHandlerTest extends TestCase
{
    public function testHandler()
    {
        $loop = Factory::create();
        $command = new JsonEncodeCommand([]);
        $service = new JsonEncodeService($loop);
        $handler = new JsonEncodeHandler($service);
        self::assertSame('[]', await($handler->handle($command), $loop));
    }
}
