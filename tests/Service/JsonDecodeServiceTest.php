<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport\Service;

use ApiClients\Foundation\Transport\Service\JsonDecodeService;
use ApiClients\Tools\TestUtilities\TestCase;
use React\EventLoop\Factory;
use function Clue\React\Block\await;

class JsonDecodeServiceTest extends TestCase
{
    public function testHandler()
    {
        $json = [
            'foo' => 'bar',
        ];
        $loop = Factory::create();
        $service = new JsonDecodeService($loop);
        $this->assertSame($json, await($service->handle(json_encode($json)), $loop));
    }
}
