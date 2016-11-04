<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport\CommandBus\Handler;

use ApiClients\Foundation\Transport\CommandBus\Command\JsonDecodeCommand;
use ApiClients\Foundation\Transport\Service\JsonDecodeService;
use React\Promise\PromiseInterface;
use function WyriHaximus\React\futureFunctionPromise;

final class JsonDecodeHandler
{
    /**
     * @var JsonDecodeService
     */
    private $jsonDecodeService;

    /**
     * @param JsonDecodeService $jsonDecodeService
     */
    public function __construct(JsonDecodeService $jsonDecodeService)
    {
        $this->jsonDecodeService = $jsonDecodeService;
    }

    /**
     * @param JsonDecodeCommand $command
     * @return PromiseInterface
     */
    public function handle(JsonDecodeCommand $command): PromiseInterface
    {
        return $this->jsonDecodeService->handle($command->getJson());
    }
}
