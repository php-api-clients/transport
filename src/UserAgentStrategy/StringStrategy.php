<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport\UserAgentStrategy;

use ApiClients\Foundation\Transport\Options;
use ApiClients\Foundation\Transport\UserAgentStrategyInterface;
use InvalidArgumentException;

final class StringStrategy implements UserAgentStrategyInterface
{
    public function determineUserAgent(array $options): string
    {
        if (!isset($options[Options::USER_AGENT])) {
            throw new InvalidArgumentException('Missing user agent option');
        }

        return $options[Options::USER_AGENT];
    }
}
