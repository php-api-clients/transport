<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport;

use Psr\Http\Message\RequestInterface;
use React\Promise\PromiseInterface;

interface ClientInterface
{
    public function request(RequestInterface $request, array $options = []): PromiseInterface;
}
