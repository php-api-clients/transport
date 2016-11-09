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
        $this->assertSame([], $stream->getJson());
        $this->assertSame('', (string)$stream);
        $this->assertSame('', $stream->getContents());
        $this->assertSame(0, $stream->getSize());
        $this->assertSame(true, $stream->isReadable());
        $this->assertSame(false, $stream->isWritable());
        $this->assertSame(false, $stream->isSeekable());
        $this->assertSame(true, $stream->eof());
        $this->assertSame([], $stream->getMetadata());

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
