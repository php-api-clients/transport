<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport\UserAgentStrategy;

use ApiClients\Foundation\Transport\Options;
use ApiClients\Foundation\Transport\UserAgentStrategyInterface;
use InvalidArgumentException;
use PackageVersions\Versions;
use function Composed\package;

final class PackageVersionStrategy implements UserAgentStrategyInterface
{
    const USER_AGENT = '%s API client %s%s powered by PHP API Clients https://php-api-clients.org/';

    public function determineUserAgent(array $options): string
    {
        if (!isset($options[Options::PACKAGE])) {
            throw new InvalidArgumentException('Missing package option');
        }

        $package = $options[Options::PACKAGE];

        $chunks = [];
        $chunks[] = $package;
        $chunks[] = explode('@', Versions::getVersion($package))[0];
        $chunks[] = $this->getWebsite($package);

        return sprintf(
            self::USER_AGENT,
            ...$chunks
        );
    }

    protected function getWebsite(string $package)
    {
        $package = package($package);
        $homepage = $package->getConfig('homepage');

        if ($homepage === null) {
            return '';
        }

        if (filter_var($homepage, FILTER_VALIDATE_URL) === false) {
            return '';
        }

        return ' (' . $homepage . ') ';
    }
}
