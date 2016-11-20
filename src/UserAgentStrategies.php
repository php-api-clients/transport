<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport;

use ApiClients\Foundation\Transport\UserAgentStrategy\PackageVersionStrategy;
use ApiClients\Foundation\Transport\UserAgentStrategy\StringStrategy;

final class UserAgentStrategies
{
    const STRING                    = StringStrategy::class;
    const PACKAGE_VERSION           = PackageVersionStrategy::class;
}
