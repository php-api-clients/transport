<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport\Middleware;

use ApiClients\Foundation\Transport\JsonStream;
use ApiClients\Foundation\Transport\Middleware\JsonEncodeMiddleware;
use ApiClients\Foundation\Transport\Service\JsonEncodeService;
use ApiClients\Tools\TestUtilities\TestCase;
use function Clue\React\Block\await;
use GuzzleHttp\Psr7\BufferStream;
use GuzzleHttp\Psr7\Request;
use React\EventLoop\Factory;

class JsonEncodeMiddlewareTest extends TestCase
{
    public function testPre()
    {
        $loop = Factory::create();
        $service = new JsonEncodeService($loop);
        $middleware = new JsonEncodeMiddleware($service);
        $stream = new JsonStream([]);
        $request = new Request('GET', 'https://example.com', [], $stream);

        $this->assertSame(
            '[]',
            (string) await(
                $middleware->pre($request),
                $loop
            )->getBody()
        );
    }

    public function testPreNoJson()
    {
        $loop = Factory::create();
        $service = new JsonEncodeService($loop);
        $middleware = new JsonEncodeMiddleware($service);
        $stream = new BufferStream(2);
        $stream->write('yo');
        $request = new Request('GET', 'https://example.com', [], $stream);

        $this->assertSame(
            $request,
            await(
                $middleware->pre($request),
                $loop
            )
        );
    }
}
