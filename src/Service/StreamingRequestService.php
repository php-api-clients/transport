<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport\Service;

use ApiClients\Foundation\Service\ServiceInterface;
use ApiClients\Foundation\Transport\ClientInterface;
use ApiClients\Foundation\Transport\StreamingResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Promise\CancellablePromiseInterface;
use function React\Promise\resolve;

final class StreamingRequestService
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
