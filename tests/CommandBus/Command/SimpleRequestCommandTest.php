<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport\CommandBus\Command;

use ApiClients\Foundation\Transport\CommandBus\Command\SimpleRequestCommand;
use ApiClients\Tools\TestUtilities\TestCase;
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
        $command = new SimpleRequestCommand($path, [
            'option' => 'value',
        ], $refresh);
        self::assertInstanceOf(RequestInterface::class, $command->getRequest());
        self::assertSame($method, $command->getRequest()->getMethod());
        self::assertSame($path, $command->getRequest()->getUri()->getPath());
        self::assertSame([
            'option' => 'value',
        ], $command->getOptions());
    }

    public function testCommandDefaults()
    {
        $method = 'GET';
        $path = '/foo/bar.json';
        $refresh = false;
        $command = new SimpleRequestCommand($path);
        self::assertInstanceOf(RequestInterface::class, $command->getRequest());
        self::assertSame($method, $command->getRequest()->getMethod());
        self::assertSame($path, $command->getRequest()->getUri()->getPath());
        self::assertSame([], $command->getOptions());
    }
}
