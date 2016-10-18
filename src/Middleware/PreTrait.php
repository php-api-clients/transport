<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport\RequestMiddleware;

use Psr\Http\Message\RequestInterface;
use React\Promise\CancellablePromiseInterface;
use function React\Promise\resolve;

trait PreTrait
{
    /**
     * @param RequestInterface $request
     * @param array $options
     * @return CancellablePromiseInterface
     */
    public function pre(RequestInterface $request, array $options = []): CancellablePromiseInterface
    {
        return resolve($request);
    }
}
