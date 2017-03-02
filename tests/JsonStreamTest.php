<?php
declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Transport;

use ApiClients\Foundation\Transport\JsonStream;
use ApiClients\Tools\TestUtilities\TestCase;
use RuntimeException;

class JsonStreamTest extends TestCase
{
    public function testBasics()
    {
        $stream = new JsonStream([]);
        self::assertSame([], $stream->getJson());
        self::assertSame('', (string)$stream);
        self::assertSame('', $stream->getContents());
        self::assertSame(0, $stream->getSize());
        self::assertSame(true, $stream->isReadable());
        self::assertSame(false, $stream->isWritable());
        self::assertSame(false, $stream->isSeekable());
        self::assertSame(true, $stream->eof());
        self::assertSame([], $stream->getMetadata());

        $stream->close();
        $stream->detach();
        $stream->rewind();
    }

    public function testSeek()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot seek a BufferStream');
        (new JsonStream([]))->seek(0);
    }

    public function testTell()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot determine the position of a BufferStream');
        (new JsonStream([]))->tell();
    }

    public function testRead()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot read from a JsonStream');
        (new JsonStream([]))->read(0);
    }

    public function testWrite()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot write to a JsonStream');
        (new JsonStream([]))->write('');
    }
}
