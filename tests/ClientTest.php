<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport;

use ApiClients\Foundation\Transport\Client;
use ApiClients\Foundation\Transport\Options;
use ApiClients\Tools\TestUtilities\TestCase;
use Clue\React\Buzz\Browser as BuzzClient;
use GuzzleHttp\Psr7\Request;
use League\Container\Container;
use Phake;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use React\EventLoop\Factory;
use React\Promise\FulfilledPromise;
use function Clue\React\Block\await;
use function React\Promise\resolve;

class ClientTest extends TestCase
{
    public function provideRequests()
    {
        yield [
            new Request('GET', ''),
            new Request('GET', 'http://api.example.com/', [
                'User-Agent' => 'WyriHaximus/php-api-client',
            ]),
        ];

        yield [
            new Request('GET', 'status'),
            new Request('GET', 'http://api.example.com/status', [
                'User-Agent' => 'WyriHaximus/php-api-client',
            ]),
        ];

        yield [
            new Request('HEAD', 'https://api.example.com/status'),
            new Request('HEAD', 'https://api.example.com/status', [
                'User-Agent' => 'WyriHaximus/php-api-client',
            ]),
        ];
    }

    /**
     * @dataProvider provideRequests
     */
    public function testRequest(RequestInterface $inputRequest, RequestInterface $outputRequest)
    {
        $container = new Container();
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
        Phake::when($handler)->send($this->isInstanceOf(Request::class))->thenReturnCallback(function (RequestInterface $guzzleRequest) use ($response, &$request) {
            $request = $guzzleRequest;
            return new FulfilledPromise($response);
        });

        $client = new Client(
            $loop,
            $container,
            $handler,
            [
                Options::SCHEMA => 'http',
                Options::HOST => 'api.example.com',
            ]
        );

        $client->request($inputRequest, [], true);

        $this->assertSame($outputRequest->getMethod(), $request->getMethod());
        $this->assertSame((string) $outputRequest->getUri(), (string) $request->getUri());

        $headers = $request->getHeaders();
        ksort($headers);
        $outputHeaders = $request->getHeaders();
        ksort($outputHeaders);
        $this->assertSame($headers, $outputHeaders);
    }

    public function provideGetBaseURL()
    {
        yield [
            [
                'schema' => 'http',
                'host' => 'api.wyrihaximus.net',
            ],
            'http://api.wyrihaximus.net/'
        ];

        yield [
            [
                'host' => 'wyrihaximus.net',
                'path' => '/api/',
            ],
            'https://wyrihaximus.net/api/'
        ];

        yield [
            [
                'schema' => 'gopher',
                'host' => 'thorerik.com',
            ],
            'gopher://thorerik.com/'
        ];
    }

    /**
     * @dataProvider provideGetBaseURL
     */
    public function testGetBaseURL(array $options, string $baseURL)
    {
        $container = new Container();
        $loop = Factory::create();
        $handler = Phake::mock(BuzzClient::class);

        $client = new Client(
            $loop,
            $container,
            $handler,
            $options
        );

        $this->assertSame($baseURL, $client->getBaseURL());
    }
}
