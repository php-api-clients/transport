<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport;

use ApiClients\Foundation\Middleware\Locator\ContainerLocator;
use ApiClients\Foundation\Middleware\Locator\Locator;
use ApiClients\Foundation\Transport\Client;
use ApiClients\Foundation\Transport\Options;
use ApiClients\Foundation\Transport\UserAgentStrategies;
use ApiClients\Foundation\Transport\UserAgentStrategy\PackageVersionStrategy;
use ApiClients\Foundation\Transport\UserAgentStrategyInterface;
use ApiClients\Tools\TestUtilities\TestCase;
use Clue\React\Buzz\Browser as BuzzClient;
use DI\ContainerBuilder;
use Exception;
use InvalidArgumentException;
use PackageVersions\Versions;
use Phake;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use React\EventLoop\Factory;
use React\Promise\FulfilledPromise;
use RingCentral\Psr7\Request;
use function Clue\React\Block\await;
use function React\Promise\reject;
use function React\Promise\resolve;

class ClientTest extends TestCase
{
    public function provideRequests()
    {
        yield [
            new Request('GET', ''),
            new Request('GET', 'http://api.example.com/'),
        ];

        yield [
            new Request('GET', 'status'),
            new Request('GET', 'http://api.example.com/status'),
        ];

        yield [
            new Request('HEAD', 'https://api.example.com/status'),
            new Request('HEAD', 'https://api.example.com/status'),
        ];
    }

    /**
     * @dataProvider provideRequests
     */
    public function testRequest(RequestInterface $inputRequest, RequestInterface $outputRequest)
    {
        $locator = Phake::mock(Locator::class);
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
        $handler = Phake::mock(BuzzClient::class);
        Phake::when($handler)->send($outputRequest)->thenReturnCallback(function (RequestInterface $guzzleRequest) use ($response, &$request) {
            $request = $guzzleRequest;
            return new FulfilledPromise($response);
        });

        $client = new Client(
            $loop,
            $locator,
            $handler,
            [
                Options::SCHEMA => 'http',
                Options::HOST => 'api.example.com',
            ]
        );

        $client->request($inputRequest, [], true);

        Phake::verify($handler)->send($outputRequest);

        self::assertNotFalse($request);
        self::assertInstanceOf(RequestInterface::class, $request);

        self::assertSame($outputRequest->getMethod(), $request->getMethod());
        self::assertSame((string) $outputRequest->getUri(), (string) $request->getUri());

        $headers = $outputRequest->getHeaders();
        ksort($headers);
        $outputHeaders = $request->getHeaders();
        ksort($outputHeaders);
        self::assertSame($headers, $outputHeaders);
    }

    /**
     * @dataProvider provideRequests
     */
    public function testError(RequestInterface $inputRequest, RequestInterface $outputRequest)
    {
        $exceptionMessage = 'Exception turned InvalidArgumentException';
        $exception = new Exception($exceptionMessage);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $container = ContainerBuilder::buildDevContainer();
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
        Phake::when($handler)->send($outputRequest)->thenReturn(reject($exception));

        $client = new Client(
            $loop,
            new ContainerLocator($container),
            $handler,
            [
                Options::SCHEMA => 'http',
                Options::HOST => 'api.example.com',
                Options::MIDDLEWARE => [
                    DummyMiddleware::class,
                ],
            ]
        );

        await($client->request($inputRequest, [], true), $loop);
    }
}
