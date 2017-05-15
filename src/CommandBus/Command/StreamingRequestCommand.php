<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport\CommandBus\Command;

use Psr\Http\Message\RequestInterface;
use WyriHaximus\Tactician\CommandHandler\Annotations\Handler;

/**
 * @Handler("ApiClients\Foundation\Transport\CommandBus\Handler\StreamingRequestHandler")
 */
final class StreamingRequestCommand implements RequestCommandInterface
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
     * @param RequestInterface $request
     * @param array            $options
     */
    public function __construct(RequestInterface $request, array $options = [])
    {
        $this->request = $request;
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
