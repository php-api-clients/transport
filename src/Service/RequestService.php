<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport\Service;

use ApiClients\Foundation\Service\ServiceInterface;
use ApiClients\Foundation\Transport\ClientInterface;
use ApiClients\Foundation\Transport\Middleware\BufferedSinkMiddleware;
use ApiClients\Foundation\Transport\Options;
use Psr\Http\Message\RequestInterface;
use React\Promise\CancellablePromiseInterface;
use function React\Promise\resolve;

class RequestService
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param RequestInterface $request
     * @param array $options
     * @return CancellablePromiseInterface
     */
    public function request(RequestInterface $request, array $options = []): CancellablePromiseInterface
    {
        if (!isset($options[Options::MIDDLEWARE])) {
            $options[Options::MIDDLEWARE] = [];
        }

        if (!in_array(BufferedSinkMiddleware::class, $options[Options::MIDDLEWARE])) {
            $options[Options::MIDDLEWARE][] = BufferedSinkMiddleware::class;
        }

        return $this->client->request(
            $request,
            $options
        );
    }
}
