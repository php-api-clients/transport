<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport\Middleware;

use ApiClients\Foundation\Middleware\ErrorTrait;
use ApiClients\Foundation\Middleware\MiddlewareInterface;
use ApiClients\Foundation\Middleware\PreTrait;
use Psr\Http\Message\ResponseInterface;
use React\Promise\CancellablePromiseInterface;
use React\Stream\ReadableStreamInterface;
use RingCentral\Psr7\BufferStream;
use function React\Promise\resolve;
use function React\Promise\Stream\buffer;

class BufferedSinkMiddleware implements MiddlewareInterface
{
    use PreTrait;
    use ErrorTrait;

    /**
     * @param  ResponseInterface           $response
     * @param  array                       $options
     * @return CancellablePromiseInterface
     */
    public function post(
        ResponseInterface $response,
        string $transactionId,
        array $options = []
    ): CancellablePromiseInterface {
        if (!($response->getBody() instanceof ReadableStreamInterface)) {
            return resolve($response);
        }

        return buffer($response->getBody())->then(function (string $body) use ($response) {
            $stream = new BufferStream(strlen($body));
            $stream->write($body);

            return resolve($response->withBody($stream));
        });
    }
}
