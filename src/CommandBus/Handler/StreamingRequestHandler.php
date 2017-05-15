<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport\CommandBus\Handler;

use ApiClients\Foundation\Transport\CommandBus\Command\RequestCommandInterface;
use ApiClients\Foundation\Transport\Service\StreamingRequestService;
use React\Promise\PromiseInterface;

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

    /**
     * @param  RequestCommandInterface $command
     * @return PromiseInterface
     */
    public function handle(RequestCommandInterface $command): PromiseInterface
    {
        return $this->service->stream(
            $command->getRequest(),
            $command->getOptions()
        );
    }
}
