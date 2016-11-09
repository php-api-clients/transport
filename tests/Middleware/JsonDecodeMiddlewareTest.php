<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport\Middleware;

use ApiClients\Foundation\Transport\JsonStream;
use ApiClients\Foundation\Transport\Middleware\JsonDecodeMiddleware;
use ApiClients\Foundation\Transport\Service\JsonDecodeService;
use ApiClients\Tools\TestUtilities\TestCase;
use function Clue\React\Block\await;
use Clue\React\Buzz\Message\ReadableBodyStream;
use GuzzleHttp\Psr7\Response;
use React\EventLoop\Factory;
use React\Stream\ThroughStream;

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

        $this->assertInstanceOf(JsonStream::class, $body);

        $this->assertSame(
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

        $this->assertSame(
            $response,
            await(
                $middleware->post($response),
                $loop
            )
        );
    }
}
