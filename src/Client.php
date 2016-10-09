<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport;

use ApiClients\Foundation\Hydrator\Hydrator;
use ApiClients\Foundation\Transport\CommandBus;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\Psr7\Response as Psr7Response;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use React\Cache\CacheInterface;
use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\TimerInterface;
use React\Promise\PromiseInterface;
use function React\Promise\reject;
use React\Promise\RejectedPromise;
use function React\Promise\resolve;
use function WyriHaximus\React\futureFunctionPromise;

class Client
{
    const DEFAULT_OPTIONS = [
        Options::SCHEMA => 'https',
        Options::PATH => '/',
        Options::USER_AGENT => 'WyriHaximus/php-api-client',
        Options::HEADERS => [],
    ];

    /**
     * @var GuzzleClient
     */
    protected $handler;

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var Hydrator
     */
    protected $hydrator;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @param LoopInterface $loop
     * @param ContainerInterface $container
     * @param GuzzleClient $handler
     * @param array $options
     */
    public function __construct(
        LoopInterface $loop,
        ContainerInterface $container,
        GuzzleClient $handler,
        array $options = []
    ) {
        $this->loop = $loop;
        $this->handler = $handler;
        $this->options = $options + self::DEFAULT_OPTIONS;

        if (isset($this->options[Options::CACHE]) && $this->options[Options::CACHE] instanceof CacheInterface) {
            $this->cache = $this->options[Options::CACHE];
        }
    }

    /**
     * @param UriInterface $uri
     * @return PromiseInterface
     */
    protected function checkCache(UriInterface $uri): PromiseInterface
    {
        if (!($this->cache instanceof CacheInterface)) {
            return reject();
        }

        $key = $this->determineCacheKey($uri);
        return $this->cache->get($key)->then(function ($document) {
            $document = json_decode($document, true);
            $response = new Psr7Response(
                $document['status_code'],
                $document['headers'],
                $document['body'],
                $document['protocol_version'],
                $document['reason_phrase']
            );

            return resolve($response);
        });
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    protected function storeCache(RequestInterface $request, ResponseInterface $response)
    {
        if (!($this->cache instanceof CacheInterface)) {
            return;
        }

        $document = [
            'body' => $response->getBody()->getContents(),
            'headers' => $response->getHeaders(),
            'protocol_version' => $response->getProtocolVersion(),
            'reason_phrase' => $response->getReasonPhrase(),
            'status_code' => $response->getStatusCode(),
        ];

        $key = $this->determineCacheKey($request->getUri());

        $this->cache->set($key, json_encode($document));
    }

    /**
     * @param UriInterface $uri
     * @return string
     */
    protected function determineCacheKey(UriInterface $uri): string
    {
        return $this->stripExtraSlashes(
            implode(
                '/',
                [
                    $uri->getScheme(),
                    $uri->getHost(),
                    $uri->getPort(),
                    $uri->getPath(),
                    md5($uri->getQuery()),
                ]
            )
        );
    }

    /**
     * @param string $string
     * @return string
     */
    protected function stripExtraSlashes(string $string): string
    {
        return preg_replace('#/+#', '/', $string);
    }

    /**
     * @param RequestInterface $request
     * @param bool $refresh
     * @param array $options
     * @return PromiseInterface
     */
    public function request(RequestInterface $request, array $options = [], bool $refresh = false): PromiseInterface
    {
        $promise = new RejectedPromise();

        $request = $this->applyApiSettingsToRequest($request);

        if ((!isset($options[RequestOptions::STREAM]) || !$options[RequestOptions::STREAM]) && !$refresh) {
            $promise = $this->checkCache($request->getUri());
        }

        return $promise->otherwise(function () use ($request, $options) {
            return resolve($this->handler->sendAsync(
                $request,
                $options
            ));
        })->then(function (ResponseInterface $response) use ($request, $options, $refresh) {
            if (isset($options[RequestOptions::STREAM]) && $options[RequestOptions::STREAM] === true) {
                $responseWrapper = new Response('', $response);
                $this->streamBody($responseWrapper);
                return resolve($responseWrapper);
            }

            $contents = $response->getBody()->getContents();

            $this->storeCache(
                $request,
                new Psr7Response(
                    $response->getStatusCode(),
                    $response->getHeaders(),
                    $contents,
                    $response->getProtocolVersion(),
                    $response->getReasonPhrase()
                )
            );

            return resolve(
                new Response(
                    $contents,
                    new Psr7Response(
                        $response->getStatusCode(),
                        $response->getHeaders(),
                        $contents,
                        $response->getProtocolVersion(),
                        $response->getReasonPhrase()
                    )
                )
            );
        });
    }

    protected function streamBody(Response $response)
    {
        $stream = $response->getResponse()->getBody();
        $this->loop->addPeriodicTimer(0.001, function (TimerInterface $timer) use ($stream, $response) {
            if ($stream->eof()) {
                $timer->cancel();
                $response->emit('end');
                return;
            }

            $size = $stream->getSize();
            if ($size === 0) {
                return;
            }

            $response->emit('data', [$stream->read($size)]);
        });
    }

    protected function applyApiSettingsToRequest(RequestInterface $request): RequestInterface
    {
        $uri = $request->getUri();
        if (substr((string)$uri, 0, 4) !== 'http') {
            $uri = Uri::resolve(new Uri($this->getBaseURL()), $request->getUri());
        }

        return new Psr7Request(
            $request->getMethod(),
            $uri,
            $this->getHeaders() + $request->getHeaders(),
            $request->getBody(),
            $request->getProtocolVersion()
        );
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        $headers = [
            'User-Agent' => $this->options[Options::USER_AGENT],
        ];
        $headers += $this->options[Options::HEADERS];
        return $headers;
    }

    /**
     * @return string
     */
    public function getBaseURL(): string
    {
        return $this->options[Options::SCHEMA] . '://' . $this->options[Options::HOST] . $this->options[Options::PATH];
    }
}
