<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport;

interface UserAgentStrategyInterface
{
    public function determineUserAgent(array $options): string;
}
