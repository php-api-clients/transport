<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport;

final class UserAgentStrategies
{
    const STRING                    = 'string';
    const PACKAGE_VERSION           = 'package_version';
    const PACKAGE_VERSION_PLUS_HASH = 'package_version_plus_hash';
}
