<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport\CommandBus\Handler;

use ApiClients\Foundation\Transport\CommandBus\Command\JsonEncodeCommand;
use ApiClients\Foundation\Transport\Service\JsonEncodeService;
use React\Promise\PromiseInterface;
use function WyriHaximus\React\futureFunctionPromise;

final class JsonEncodeHandler
{
    /**
     * @var JsonEncodeService
     */
    private $jsonEncodeService;

    /**
     * @param JsonEncodeService $jsonEncodeService
     */
    public function __construct(JsonEncodeService $jsonEncodeService)
    {
        $this->jsonEncodeService = $jsonEncodeService;
    }

    /**
     * @param JsonEncodeCommand $command
     * @return PromiseInterface
     */
    public function handle(JsonEncodeCommand $command): PromiseInterface
    {
        return $this->jsonEncodeService->handle($command->getJson());
    }
}
