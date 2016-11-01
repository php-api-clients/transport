<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport\CommandBus\Handler;

use ApiClients\Foundation\Transport\Client;
use ApiClients\Foundation\Transport\CommandBus\Command\StreamingRequestCommand;
use ApiClients\Foundation\Transport\CommandBus\Handler\StreamingRequestHandler;
use ApiClients\Tools\TestUtilities\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use React\Promise\FulfilledPromise;
use RingCentral\Psr7\Request;

class StreamingRequestHandlerTest extends TestCase
{
    public function testHandler()
    {
        $path = '/foo/bar.json';
        $request = new Request('GET', $path);
        $client = $this->prophesize(Client::class);
        $promise = new FulfilledPromise();
        $client->request(Argument::that(function (RequestInterface $request) use ($path) {
            return $request->getUri()->getPath() === $path;
        }), [])->willReturn($promise);
        $command = new StreamingRequestCommand($request);
        $handler = new StreamingRequestHandler($client->reveal());
        $this->assertSame($promise, $handler->handle($command));
    }
}
