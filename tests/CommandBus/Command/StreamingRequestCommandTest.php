<?php
declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport\CommandBus\Command;

use ApiClients\Foundation\Transport\CommandBus\Command\StreamingRequestCommand;
use ApiClients\Tools\TestUtilities\TestCase;
use Psr\Http\Message\RequestInterface;

class StreamingRequestCommandTest extends TestCase
{
    /**
     * @dataProvider provideTrueFalse
     */
    public function testCommand(bool $refresh)
    {
        $request = $this->prophesize(RequestInterface::class)->reveal();
        $command = new StreamingRequestCommand($request, [
            'option' => 'value',
        ], $refresh);
        $this->assertSame($request, $command->getRequest());
        $this->assertSame([
            'option' => 'value',
        ], $command->getOptions());
    }

    public function testCommandDefaults()
    {
        $request = $this->prophesize(RequestInterface::class)->reveal();
        $command = new StreamingRequestCommand($request);
        $this->assertSame($request, $command->getRequest());
        $this->assertSame([], $command->getOptions());
    }
}
