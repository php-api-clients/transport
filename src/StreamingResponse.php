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
     * @param SchedulerInterface $scheduler
     * @return DisposableInterface
     */
    public function subscribe(ObserverInterface $observer, SchedulerInterface $scheduler = null): DisposableInterface
    {
        $body = $this->response->getBody();
        $body->on('data', function (string $data) use ($observer) {
            $observer->onNext($data);
        });
        $body->on('end', function () use ($observer) {
            $observer->onCompleted();
        });
        $body->on('error', function ($error) use ($observer) {
            $observer->onError($error);
        });

        return new EmptyDisposable();
    }
}
