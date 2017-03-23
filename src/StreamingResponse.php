<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport;

use Psr\Http\Message\ResponseInterface;
use Rx\Disposable\EmptyDisposable;
use Rx\DisposableInterface;
use Rx\Observable;
use Rx\ObserverInterface;
use Rx\SchedulerInterface;

final class StreamingResponse extends Observable
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @param ObserverInterface $observer
     * @return DisposableInterface
     * @ignoreCodeCoverage
     */
    // @codingStandardsIgnoreStart
    protected function _subscribe(ObserverInterface $observer): DisposableInterface
    {
        // @codingStandardsIgnoreEnd
        $body = $this->response->getBody();
        $body->on('data', [$observer, 'onNext']);
        $body->on('error', [$observer, 'onError']);
        $body->on('end', [$observer, 'onCompleted']);

        return new EmptyDisposable();
    }
}
