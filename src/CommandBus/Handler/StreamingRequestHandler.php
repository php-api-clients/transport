<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport\CommandBus\Handler;

use ApiClients\Foundation\Transport\CommandBus\Command\RequestCommandInterface;
use ApiClients\Foundation\Transport\Service\StreamingRequestService;
use React\Promise\PromiseInterface;
use function React\Promise\resolve;

final class StreamingRequestHandler
{
    /**
     * @var StreamingRequestService
     */
    private $service;

    /**
     * @param StreamingRequestService $service
     */
    public function __construct(StreamingRequestService $service)
    {
        $this->service = $service;
    }

    public function handle(RequestCommandInterface $command): PromiseInterface
    {
        return $this->service->handle(
            $command->getRequest(),
            $command->getOptions()
        );
    }
}
