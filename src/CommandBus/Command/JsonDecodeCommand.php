<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport\CommandBus\Command;

use WyriHaximus\Tactician\CommandHandler\Annotations\Handler;

/**
 * @Handler("ApiClients\Foundation\Transport\CommandBus\Handler\JsonDecodeHandler")
 */
final class JsonDecodeCommand
{
    /**
     * @var string
     */
    private $json;

    /**
     * @param string $json
     */
    public function __construct(string $json)
    {
        $this->json = $json;
    }

    /**
     * @return string
     */
    public function getJson(): string
    {
        return $this->json;
    }
}
