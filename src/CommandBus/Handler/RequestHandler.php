<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport\CommandBus\Handler;

use ApiClients\Foundation\Transport\Client;
use ApiClients\Foundation\Transport\CommandBus\Command\RequestCommandInterface;
use ApiClients\Foundation\Transport\Middleware\BufferedSinkMiddleware;
use ApiClients\Foundation\Transport\Options;
use React\Promise\PromiseInterface;
use function React\Promise\resolve;

final class RequestHandler
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

    public function handle(RequestCommandInterface $command): PromiseInterface
    {
        $options = $command->getOptions();

        if (!isset($options[Options::MIDDLEWARE])) {
            $options[Options::MIDDLEWARE] = [];
        }

        if (!in_array(BufferedSinkMiddleware::class, $options[Options::MIDDLEWARE])) {
            $options[Options::MIDDLEWARE][] = BufferedSinkMiddleware::class;
        }

        return $this->client->request(
            $command->getRequest(),
            $options
        );
    }
}
