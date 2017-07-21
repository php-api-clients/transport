<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport;

use ApiClients\Foundation\Middleware\Locator\ContainerLocator;
use ApiClients\Foundation\Middleware\Locator\Locator;
use ApiClients\Foundation\Transport\Client;
use ApiClients\Foundation\Transport\Options;
use ApiClients\Tools\TestUtilities\TestCase;
use Clue\React\Buzz\Browser as BuzzClient;
use Clue\React\Buzz\Message\ReadableBodyStream;
use DI\ContainerBuilder;
use Exception;
use InvalidArgumentException;
use Phake;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use React\EventLoop\Factory;
use React\Promise\Deferred;
use React\Promise\Promise;
use RingCentral\Psr7\BufferStream;
use RingCentral\Psr7\Request;
use function Clue\React\Block\await;
use function React\Promise\reject;
use function React\Promise\resolve;

class ClientTest extends TestCase
{
    public function provideRequests()
    {
        $defaultClientOptions = [
            Options::SCHEMA => 'http',
            Options::HOST => 'api.example.com',
            Options::MIDDLEWARE => [
                DummyMiddleware::class,
            ],
        ];
        $defaultRequestOptions = [];

        yield [
            new Request('GET', '', [], new BufferStream()),
            new Request('GET', 'http://api.example.com/', [], new BufferStream()),
            $defaultClientOptions,
            $defaultRequestOptions,
        ];

        yield [
            new Request('GET', 'status', [], new BufferStream()),
            new Request('GET', 'http://api.example.com/status', [], new BufferStream()),
            $defaultClientOptions,
            $defaultRequestOptions,
        ];

        yield [
            new Request('HEAD', 'https://api.example.com/status', [], new BufferStream()),
            new Request('HEAD', 'https://api.example.com/status', [], new BufferStream()),
            $defaultClientOptions,
            $defaultRequestOptions,
        ];

        yield [
            new Request('HEAD', 'https://api.example.com/status', [], new BufferStream()),
            new Request('HEAD', 'https://api.example.com/status', ['Accept' => 'foo',], new BufferStream()),
            $defaultClientOptions + [
                Options::HEADERS => [
                    'Accept' => 'foo',
                ],
            ],
            $defaultRequestOptions,
        ];

        yield [
            new Request('HEAD', 'https://api.example.com/status', [], new BufferStream()),
            new Request('HEAD', 'https://api.example.com/status', ['Accept' => 'bar',], new BufferStream()),
            $defaultClientOptions + [
                Options::HEADERS => [
                    'Accept' => 'foo',
                ],
            ],
            $defaultRequestOptions + [
                Options::HEADERS => [
                    'Accept' => 'bar',
                ],
            ],
        ];

        yield [
            new Request('HEAD', 'https://api.example.com/status', [], new BufferStream()),
            new Request(
                'HEAD',
                'https://api.example.com/status',
                ['Accept' => 'foo',' Decline' => 'bar',],
                new BufferStream()
            ),
            $defaultClientOptions + [
                Options::HEADERS => [
                    'Accept' => 'foo',
                ],
            ],
            $defaultRequestOptions + [
                Options::HEADERS => [
                    'Decline' => 'bar',
                ],
            ],
        ];

        yield [
            new Request('HEAD', 'https://api.example.com/status', [], new BufferStream()),
            new Request(
                'HEAD',
                'https://api.example.com/status',
                ['Accept' => 'bar',' Decline' => 'bar',],
                new BufferStream()
            ),
            $defaultClientOptions + [
                Options::HEADERS => [
                    'Accept' => 'foo',
                ],
            ],
            $defaultRequestOptions + [
                Options::HEADERS => [
                    'Accept' => 'bar',
                    'Decline' => 'bar',
                ],
            ],
        ];

        yield [
            new Request('HEAD', 'https://api.example.com/status', [], new BufferStream()),
            new Request(
                'HEAD',
                'https://api.example.com/status',
                ['a' => 'b',' c' => 'd', 'e' => 'f', 'g' => 'h',],
                new BufferStream()
            ),
            $defaultClientOptions + [
                Options::HEADERS => [
                    'a' => 'b',
                    'c' => 'd',
                ],
            ],
            $defaultRequestOptions + [
                Options::HEADERS => [
                    'e' => 'f',
                    'g' => 'h',
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideRequests
     */
    public function testRequest(
        RequestInterface $inputRequest,
        RequestInterface $outputRequest,
        array $clientOptions,
        array $requestOptions
    ) {
        $locator = $this->prophesize(Locator::class);
        $locator->get(DummyMiddleware::class)->shouldBeCalled()->willReturn(new DummyMiddleware());

        $loop = Factory::create();

        $stream = Phake::mock(StreamInterface::class);
        Phake::when($stream)->getContents()->thenReturn('{"foo":"bar"}');

        $response = Phake::mock(ResponseInterface::class);
        Phake::when($response)->getBody()->thenReturn($stream);
        Phake::when($response)->getStatusCode()->thenReturn(200);
        Phake::when($response)->getHeaders()->thenReturn([]);
        Phake::when($response)->getProtocolVersion()->thenReturn('1.1');
        Phake::when($response)->getReasonPhrase()->thenReturn('OK');

        $request = false;
        $buzz = Phake::mock(BuzzClient::class);
        Phake::when($buzz)->send(
            Phake::anyParameters()
        )->thenReturnCallback(function (RequestInterface $guzzleRequest) use ($response, &$request, $loop) {
            $request = $guzzleRequest;
            return new Promise(function ($resolve, $reject) use ($response, $loop) {
                $loop->addTimer(0.01, function () use ($response, $resolve) {
                    $resolve($response);
                });
            });
        });

        $client = new Client(
            $loop,
            $locator->reveal(),
            $buzz,
            $clientOptions
        );

        await($client->request($inputRequest, $requestOptions), $loop);

        Phake::verify($buzz)->send($outputRequest);

        self::assertNotFalse($request);
        self::assertInstanceOf(RequestInterface::class, $request);

        self::assertSame($outputRequest->getMethod(), $request->getMethod());
        self::assertSame((string) $outputRequest->getUri(), (string) $request->getUri());
        self::assertSame($outputRequest->getHeaders(), $request->getHeaders());

        $headers = $outputRequest->getHeaders();
        ksort($headers);
        $outputHeaders = $request->getHeaders();
        ksort($outputHeaders);
        self::assertSame($headers, $outputHeaders);
    }

    /**
     * @dataProvider provideRequests
     */
    public function testError(
        RequestInterface $inputRequest,
        RequestInterface $outputRequest,
        array $clientOptions,
        array $requestOptions
    ) {
        $exceptionMessage = 'Exception turned InvalidArgumentException';
        $exception = new Exception($exceptionMessage);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $locator = $this->prophesize(Locator::class);
        $locator->get(DummyMiddleware::class)->shouldBeCalled()->willReturn(new DummyMiddleware());

        $loop = Factory::create();

        $stream = Phake::mock(StreamInterface::class);
        Phake::when($stream)->getContents()->thenReturn('{"foo":"bar"}');

        $response = Phake::mock(ResponseInterface::class);
        Phake::when($response)->getBody()->thenReturn($stream);
        Phake::when($response)->getStatusCode()->thenReturn(200);
        Phake::when($response)->getHeaders()->thenReturn([]);
        Phake::when($response)->getProtocolVersion()->thenReturn('1.1');
        Phake::when($response)->getReasonPhrase()->thenReturn('OK');

        $handler = Phake::mock(BuzzClient::class);
        Phake::when($handler)->send($outputRequest)->thenReturn(
            new Promise(function ($resolve, $reject) use ($exception, $loop) {
                $loop->addTimer(0.01, function () use ($exception, $reject) {
                    $reject($exception);
                });
            })
        );

        $client = new Client(
            $loop,
            $locator->reveal(),
            $handler,
            $clientOptions
        );

        await($client->request($inputRequest, $requestOptions, true), $loop);
    }

    public function testPauseReadableStream()
    {
        $exceptionMessage = 'Exception turned InvalidArgumentException';
        $exception = new Exception($exceptionMessage);

        self::expectException(Exception::class);
        self::expectExceptionMessage($exceptionMessage);

        $loop = Factory::create();
        $locator = $this->prophesize(Locator::class);

        $stream = $this->prophesize(ReadableBodyStream::class);
        $stream->pause()->shouldBeCalled();
        $stream->resume()->shouldBeCalled();

        $request = new Request('HEAD', 'https://api.example.com/status', [], $stream->reveal());

        $deferred = new Deferred();
        $loop->addTimer(0.01, function () use ($deferred, $exception) {
            $deferred->reject($exception);
        });

        $handler = Phake::mock(BuzzClient::class);
        Phake::when($handler)->send($request)->thenReturn($deferred->promise());

        $client = new Client(
            $loop,
            $locator->reveal(),
            $handler,
            []
        );


        await($client->request($request, [], true), $loop);
    }
}
