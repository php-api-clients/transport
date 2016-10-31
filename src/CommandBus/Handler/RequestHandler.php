<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport\CommandBus\Handler;

use ApiClients\Foundation\Transport\Client;
use ApiClients\Foundation\Transport\CommandBus\Command\RequestCommandInterface;
use Psr\Http\Message\ResponseInterface;
use React\Promise\PromiseInterface;
use React\Stream\BufferedSink;
use RingCentral\Psr7\BufferStream;
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
        return $this->client->request(
            $command->getRequest(),
            $command->getOptions()
        )->then(function (ResponseInterface $response) {
            return BufferedSink::createPromise($response->getBody())->then(function (string $body) use ($response) {
                $stream = new BufferStream(strlen($body));
                $stream->write($body);
                return resolve($response->withBody($stream));
            });
        });
    }
}
