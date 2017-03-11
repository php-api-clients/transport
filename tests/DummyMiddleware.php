<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport;

use ApiClients\Foundation\Middleware\DefaultPriorityTrait;
use ApiClients\Foundation\Middleware\MiddlewareInterface;
use ApiClients\Foundation\Middleware\PostTrait;
use ApiClients\Foundation\Middleware\PreTrait;
use InvalidArgumentException;
use React\Promise\CancellablePromiseInterface;
use Throwable;
use function React\Promise\reject;

class DummyMiddleware implements MiddlewareInterface
{
    use DefaultPriorityTrait;
    use PreTrait;
    use PostTrait;

    public function error(Throwable $throwable, array $options = []): CancellablePromiseInterface
    {
        return reject(new InvalidArgumentException($throwable->getMessage(), $throwable->getCode(), $throwable));
    }
}
