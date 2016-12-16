<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport\Service;

use ApiClients\Foundation\Service\ServiceInterface;
use ApiClients\Foundation\Transport\Client;
use ApiClients\Foundation\Transport\Middleware\BufferedSinkMiddleware;
use ApiClients\Foundation\Transport\Options;
use Psr\Http\Message\RequestInterface;
use React\Promise\CancellablePromiseInterface;
use function React\Promise\resolve;

final class RequestService implements ServiceInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param RequestInterface $request
     * @param array $options
     * @return CancellablePromiseInterface
     */
    public function handle(RequestInterface $request = null, array $options = []): CancellablePromiseInterface
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
