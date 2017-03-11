<?php
declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport;

use ApiClients\Foundation\Transport\StreamingResponse;
use ApiClients\Tools\TestUtilities\TestCase;
use Clue\React\Buzz\Message\ReadableBodyStream;
use Exception;
use React\EventLoop\Factory;
use React\Stream\ThroughStream;
use RingCentral\Psr7\Response;
use Rx\React\Promise;
use function Clue\React\Block\await;
use function React\Promise\resolve;

class StreamingResponseTest extends TestCase
{
    public function testResponse()
    {
        $string = 'foo.bar';
        $loop = Factory::create();
        $stream = new ThroughStream();
        $loop->futureTick(function () use ($stream, $string) {
            $stream->end($string);
        });
        $psr7Response = new Response(200, [], new ReadableBodyStream($stream));
        $response = new StreamingResponse($psr7Response);
        self::assertSame($psr7Response, $response->getResponse());
        $result = await(Promise::fromObservable(Promise::toObservable(resolve($response))->switchLatest()), $loop);
        self::assertSame($string, $result);
    }

    public function testResponseError()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('foo.bar');

        $exception = new Exception('foo.bar');
        $loop = Factory::create();
        $stream = new ThroughStream();
        $loop->futureTick(function () use ($stream, $exception) {
            $stream->emit('error', [$exception]);
        });
        $psr7Response = new Response(200, [], new ReadableBodyStream($stream));
        $response = new StreamingResponse($psr7Response);
        await(Promise::fromObservable(Promise::toObservable(resolve($response))->switchLatest()), $loop);
    }
}
