<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport\UserAgentStrategy;

use ApiClients\Foundation\Transport\Options;
use ApiClients\Foundation\Transport\UserAgentStrategy\PackageVersionStrategy;
use ApiClients\Tools\TestUtilities\TestCase;
use InvalidArgumentException;
use PackageVersions\Versions;

final class PackageVersionStrategyTest extends TestCase
{
    public function testWorking()
    {
        $this->assertSame(
            'api-clients/transport API client ' . explode('@', Versions::getVersion('api-clients/transport'))[0] . ' powered by PHP API Clients https://php-api-clients.org/',
            (new PackageVersionStrategy())->determineUserAgent([
                Options::PACKAGE => 'api-clients/transport',
            ])
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing package option
     */
    public function testFail()
    {
        (new PackageVersionStrategy())->determineUserAgent([]);
    }
}
