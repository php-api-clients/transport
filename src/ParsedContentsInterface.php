<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport;

interface ParsedContentsInterface
{
    /**
     * Return the parsed body as array.
     *
     * @return array
     */
    public function getParsedContents(): array;
}
