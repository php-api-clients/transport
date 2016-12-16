<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport\Service;

use ApiClients\Foundation\Service\ServiceInterface;
use ApiClients\Foundation\Transport\Client;
use ApiClients\Foundation\Transport\StreamingResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Promise\CancellablePromiseInterface;
use function React\Promise\resolve;

final class StreamingRequestService implements ServiceInterface
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
        return $this->client->request(
            $request,
            $options
        )->then(function (ResponseInterface $response) {
            return resolve(new StreamingResponse($response));
        });
    }
}
