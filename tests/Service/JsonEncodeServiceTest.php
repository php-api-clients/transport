<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport\CommandBus\Handler;

use ApiClients\Foundation\Transport\Service\JsonEncodeService;
use ApiClients\Tools\TestUtilities\TestCase;
use ExceptionalJSON\EncodeErrorException;
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

    public function testFailure()
    {
        $this->expectException(EncodeErrorException::class);
        $this->expectExceptionMessage('Malformed UTF-8 characters, possibly incorrectly encoded');

        $loop = Factory::create();
        $handler = new JsonEncodeService($loop);
        await($handler->handle(["\xB1\x31"]), $loop);
    }

    public function testNoJson()
    {
        $loop = Factory::create();
        $handler = new JsonEncodeService($loop);
        $this->assertSame('foo.bar', await($handler->handle('foo.bar'), $loop));
    }
}
