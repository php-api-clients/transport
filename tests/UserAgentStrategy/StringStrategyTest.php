<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport\UserAgentStrategy;

use ApiClients\Foundation\Transport\Options;
use ApiClients\Foundation\Transport\UserAgentStrategy\StringStrategy;
use ApiClients\Tools\TestUtilities\TestCase;
use InvalidArgumentException;

final class StringStrategyTest extends TestCase
{
    public function testWorking()
    {
        $userAgent = 'abc';

        $this->assertSame(
            $userAgent,
            (new StringStrategy())->determineUserAgent([
                Options::USER_AGENT => $userAgent,
            ])
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing user agent option
     */
    public function testFail()
    {
        (new StringStrategy())->determineUserAgent([]);
    }
}
