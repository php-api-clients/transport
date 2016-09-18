<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport\CommandBus\Command;

use ApiClients\Foundation\Transport\CommandBus\Command\SimpleRequestCommand;
use ApiClients\Tests\Foundation\TestCase;
use Psr\Http\Message\RequestInterface;

class SimpleRequestCommandTest extends TestCase
{
    /**
     * @dataProvider provideTrueFalse
     */
    public function testCommand(bool $refresh)
    {
        $method = 'GET';
        $path = '/foo/bar.json';
        $command = new SimpleRequestCommand($path, $refresh);
        $this->assertInstanceOf(RequestInterface::class, $command->getRequest());
        $this->assertSame($method, $command->getRequest()->getMethod());
        $this->assertSame($path, $command->getRequest()->getUri()->getPath());
        $this->assertSame($refresh, $command->getRefresh());
    }

    public function testCommandDefaultRefresh()
    {
        $method = 'GET';
        $path = '/foo/bar.json';
        $refresh = false;
        $command = new SimpleRequestCommand($path);
        $this->assertInstanceOf(RequestInterface::class, $command->getRequest());
        $this->assertSame($method, $command->getRequest()->getMethod());
        $this->assertSame($path, $command->getRequest()->getUri()->getPath());
        $this->assertSame($refresh, $command->getRefresh());
    }
}
