<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport;

use Psr\Http\Message\StreamInterface;
use RuntimeException;

class JsonStream implements StreamInterface
{
    /**
     * @var array
     */
    private $json = [];

    public function __construct(array $json)
    {
        $this->json = $json;
    }

    /**
     * @return array
     */
    public function getJson()
    {
        return $this->json;
    }

    public function __toString()
    {
        return $this->getContents();
    }

    public function getContents()
    {
        return '';
    }

    public function close()
    {
    }

    public function detach()
    {
    }

    public function getSize()
    {
        return count($this->json);
    }

    public function isReadable()
    {
        return true;
    }

    public function isWritable()
    {
        return false;
    }

    public function isSeekable()
    {
        return false;
    }

    public function rewind()
    {
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        throw new RuntimeException('Cannot seek a BufferStream');
    }

    public function eof()
    {
        return true;
    }

    public function tell()
    {
        throw new RuntimeException('Cannot determine the position of a BufferStream');
    }

    /**
     * Reads data from the buffer.
     */
    public function read($length)
    {
        throw new RuntimeException('Cannot read from a JsonStream');
    }

    /**
     * Writes data to the buffer.
     */
    public function write($string)
    {
        throw new RuntimeException('Cannot write to a JsonStream');
    }

    public function getMetadata($key = null)
    {
        return [];
    }
}
