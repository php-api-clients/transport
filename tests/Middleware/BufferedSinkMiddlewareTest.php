<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport\Middleware;

use ApiClients\Foundation\Transport\Middleware\BufferedSinkMiddleware;
use ApiClients\Tools\TestUtilities\TestCase;
use function Clue\React\Block\await;
use Clue\React\Buzz\Message\ReadableBodyStream;
use RingCentral\Psr7\BufferStream;
use RingCentral\Psr7\Response;
use React\EventLoop\Factory;
use React\Stream\ThroughStream;

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
        $result = await($middleware->post($response), $loop);
        $this->assertInstanceOf(BufferStream::class, $result->getBody());
        $this->assertSame($bodyString, (string)$result->getBody());
    }

    public function testPostNoStream()
    {
        $loop = Factory::create();
        $middleware = new BufferedSinkMiddleware();
        $body = new BufferStream();
        $response = new Response(200, [], $body);
        $result = await($middleware->post($response), $loop);
        $this->assertSame($body, $result->getBody());
    }

    public function testPriority()
    {
        $middleware = new BufferedSinkMiddleware();
        $this->assertSame(500, $middleware->priority());
    }
}
