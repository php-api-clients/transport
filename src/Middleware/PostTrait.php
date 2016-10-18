<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport\RequestMiddleware;

use Psr\Http\Message\ResponseInterface;
use React\Promise\CancellablePromiseInterface;
use function React\Promise\resolve;

trait PostTrait
{
    /**
     * @param ResponseInterface $response
     * @param array $options
     * @return CancellablePromiseInterface
     */
    public function post(ResponseInterface $response, array $options = []): CancellablePromiseInterface
    {
        return resolve($response);
    }
}
