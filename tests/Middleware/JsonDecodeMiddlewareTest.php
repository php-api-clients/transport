<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport\Middleware;

use ApiClients\Foundation\Transport\JsonStream;
use ApiClients\Foundation\Transport\Middleware\JsonDecodeMiddleware;
use ApiClients\Foundation\Transport\Service\JsonDecodeService;
use ApiClients\Tools\TestUtilities\TestCase;
use Clue\React\Buzz\Message\ReadableBodyStream;
use React\EventLoop\Factory;
use React\Stream\ThroughStream;
use RingCentral\Psr7\Response;
use function Clue\React\Block\await;

class JsonDecodeMiddlewareTest extends TestCase
{
    public function testPost()
    {
        $loop = Factory::create();
        $service = new JsonDecodeService($loop);
        $middleware = new JsonDecodeMiddleware($service);
        $response = new Response(200, [], '[]');

        $body = await(
            $middleware->post($response),
            $loop
        )->getBody();

        self::assertInstanceOf(JsonStream::class, $body);

        self::assertSame(
            [],
            $body->getJson()
        );
    }

    public function testPostNoJson()
    {
        $loop = Factory::create();
        $service = new JsonDecodeService($loop);
        $middleware = new JsonDecodeMiddleware($service);
        $response = new Response(200, [], new ReadableBodyStream(new ThroughStream()));

        self::assertSame(
            $response,
            await(
                $middleware->post($response),
                $loop
            )
        );
    }

    public function testPriority()
    {
        $loop = Factory::create();
        $service = new JsonDecodeService($loop);
        $middleware = new JsonDecodeMiddleware($service);
        self::assertSame(1000, $middleware->priority());
    }
}
