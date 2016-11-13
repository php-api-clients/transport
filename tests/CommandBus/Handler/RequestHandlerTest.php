<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport\CommandBus\Handler;

use ApiClients\Foundation\Transport\Client;
use ApiClients\Foundation\Transport\CommandBus\Command\SimpleRequestCommand;
use ApiClients\Foundation\Transport\CommandBus\Handler\RequestHandler;
use ApiClients\Foundation\Transport\Middleware\BufferedSinkMiddleware;
use ApiClients\Foundation\Transport\Options;
use ApiClients\Tools\TestUtilities\TestCase;
use Clue\React\Buzz\Message\ReadableBodyStream;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use React\EventLoop\Factory;
use React\Promise\FulfilledPromise;
use React\Stream\ThroughStream;
use RingCentral\Psr7\Response;
use function Clue\React\Block\await;

class RequestHandlerTest extends TestCase
{
    public function testHandler()
    {
        $loop = Factory::create();
        $path = '/foo/bar.json';
        $bodyString = 'foo.bar';
        $client = $this->prophesize(Client::class);
        $stream = new ThroughStream();
        $loop->futureTick(function ()use ($stream, $bodyString) {
            $stream->end($bodyString);
        });
        $body = new ReadableBodyStream($stream);
        $response = new Response(200, [], $body);
        $promise = new FulfilledPromise($response);
        $client->request(Argument::that(function (RequestInterface $request) use ($path) {
            return $request->getUri()->getPath() === $path;
        }), [
            Options::MIDDLEWARE => [
                BufferedSinkMiddleware::class,
            ],
        ])->willReturn($promise);
        $command = new SimpleRequestCommand($path);
        $handler = new RequestHandler($client->reveal());
        $result = await($handler->handle($command), $loop);
        $this->assertEquals(200, $result->getStatusCode());
    }
}
