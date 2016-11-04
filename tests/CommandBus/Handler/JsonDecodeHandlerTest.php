<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport\CommandBus\Handler;

use ApiClients\Foundation\Transport\CommandBus\Command\JsonDecodeCommand;
use ApiClients\Foundation\Transport\CommandBus\Handler\JsonDecodeHandler;
use ApiClients\Foundation\Transport\Service\JsonDecodeService;
use ApiClients\Tools\TestUtilities\TestCase;
use React\EventLoop\Factory;
use function Clue\React\Block\await;

class JsonDecodeHandlerTest extends TestCase
{
    public function testHandler()
    {
        $json = [
            'foo' => 'bar',
        ];
        $loop = Factory::create();
        $service = new JsonDecodeService($loop);
        $command = new JsonDecodeCommand(json_encode($json));
        $handler = new JsonDecodeHandler($service);
        $this->assertSame($json, await($handler->handle($command), $loop));
    }
}
