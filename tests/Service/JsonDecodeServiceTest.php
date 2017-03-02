<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport\Service;

use ApiClients\Foundation\Transport\Service\JsonDecodeService;
use ApiClients\Tools\TestUtilities\TestCase;
use ExceptionalJSON\DecodeErrorException;
use React\EventLoop\Factory;
use function Clue\React\Block\await;

class JsonDecodeServiceTest extends TestCase
{
    public function testHandler()
    {
        $json = [
            'foo' => 'bar',
        ];
        $loop = Factory::create();
        $service = new JsonDecodeService($loop);
        self::assertSame($json, await($service->handle(json_encode($json)), $loop));
    }

    public function provideFaultyJsonStrings()
    {
        yield [
            '{\'foo\' : \'bar\'}',
            'Syntax error',
        ];

        yield [
            '',
            'Syntax error',
        ];

        yield [
            (function (): string {
                $head = '';
                $tail = '';
                for ($i = 0; $i <= 513; $i++) {
                    $head .= '[';
                    $tail .= ']';
                }
                return $head . $tail;
            })(),
            'Maximum stack depth exceeded',
        ];
    }

    /**
     * @dataProvider provideFaultyJsonStrings
     */
    public function testFailure(string $string, string $errorMessage)
    {
        $this->expectException(DecodeErrorException::class);
        $this->expectExceptionMessage($errorMessage);

        $loop = Factory::create();
        $service = new JsonDecodeService($loop);
        await($service->handle($string), $loop);
    }
}
