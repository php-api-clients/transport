<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport\Middleware;

use ApiClients\Foundation\Transport\Middleware\BufferedSinkMiddleware;
use ApiClients\Tools\TestUtilities\TestCase;
use Clue\React\Buzz\Message\ReadableBodyStream;
use React\EventLoop\Factory;
use React\Stream\ThroughStream;
use RingCentral\Psr7\BufferStream;
use RingCentral\Psr7\Response;
use function Clue\React\Block\await;

class BufferedSinkMiddlewareTest extends TestCase
{
    public function testPost()
    {
        $loop = Factory::create();
        $bodyString = 'foo.bar';
        $middleware = new BufferedSinkMiddleware();
        $stream = new ThroughStream();
        $loop->futureTick(function () use ($stream, $bodyString) {
            $stream->end($bodyString);
        });
        $body = new ReadableBodyStream($stream);
        $response = new Response(200, [], $body);
        $result = await($middleware->post($response, 'abc'), $loop);
        self::assertInstanceOf(BufferStream::class, $result->getBody());
        self::assertSame($bodyString, (string)$result->getBody());
    }

    public function testPostNoStream()
    {
        $loop = Factory::create();
        $middleware = new BufferedSinkMiddleware();
        $body = new BufferStream();
        $response = new Response(200, [], $body);
        $result = await($middleware->post($response, 'abc'), $loop);
        self::assertSame($body, $result->getBody());
    }
}
