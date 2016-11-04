<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport\CommandBus\Handler;

use ApiClients\Foundation\Transport\Service\JsonEncodeService;
use ApiClients\Tools\TestUtilities\TestCase;
use React\EventLoop\Factory;
use function Clue\React\Block\await;

class JsonEncodeServiceTest extends TestCase
{
    public function testHandler()
    {
        $loop = Factory::create();
        $handler = new JsonEncodeService($loop);
        $this->assertSame('[]', await($handler->handle([]), $loop));
    }
}
