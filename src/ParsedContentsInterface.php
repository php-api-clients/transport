<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport;

use Psr\Http\Message\StreamInterface;

interface ParsedContentsInterface extends StreamInterface
{
    /**
     * Return the parsed body as array.
     *
     * @return array
     */
    public function getParsedContents(): array;
}
