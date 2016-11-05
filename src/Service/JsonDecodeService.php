<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport\Service;

use ApiClients\Foundation\Service\ServiceInterface;
use function ExceptionalJSON\decode;
use React\EventLoop\LoopInterface;
use React\Promise\CancellablePromiseInterface;
use function WyriHaximus\React\futureFunctionPromise;

final class JsonDecodeService implements ServiceInterface
{
    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @param LoopInterface $loop
     */
    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    public function handle($input): CancellablePromiseInterface
    {
        return futureFunctionPromise($this->loop, $input, function ($json) {
            return decode($json, true);
        });
    }
}
