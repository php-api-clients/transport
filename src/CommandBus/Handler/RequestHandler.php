<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport\CommandBus\Handler;

use ApiClients\Foundation\Transport\CommandBus\Command\RequestCommandInterface;
use ApiClients\Foundation\Transport\Service\RequestService;
use React\Promise\PromiseInterface;
use function React\Promise\resolve;

final class RequestHandler
{
    /**
     * @var RequestService
     */
    private $service;

    /**
     * @param RequestService $service
     */
    public function __construct(RequestService $service)
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
