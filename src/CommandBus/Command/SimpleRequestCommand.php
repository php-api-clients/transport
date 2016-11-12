<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport\CommandBus\Command;

use RingCentral\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use WyriHaximus\Tactician\CommandHandler\Annotations\Handler;

/**
 * @Handler("ApiClients\Foundation\Transport\CommandBus\Handler\RequestHandler")
 */
final class SimpleRequestCommand implements RequestCommandInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var array
     */
    private $options;

    /**
     * @param string $path
     * @param array $options
     */
    public function __construct(string $path, array $options = [])
    {
        $this->request = new Request(
            'GET',
            $path
        );
        $this->options = $options;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
