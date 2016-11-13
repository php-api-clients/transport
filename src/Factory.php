<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport;

use ApiClients\Foundation\Events\CommandLocatorEvent;
use ApiClients\Foundation\Events\ServiceLocatorEvent;
use Clue\React\Buzz\Browser;
use Clue\React\Buzz\Io\Sender;
use Interop\Container\ContainerInterface;
use League\Event\EmitterInterface;
use React\Dns\Resolver\Factory as ResolverFactory;
use React\Dns\Resolver\Resolver;
use React\EventLoop\LoopInterface;
use React\HttpClient\Client as HttpClient;
use React\HttpClient\Factory as HttpClientFactory;

class Factory
{
    /**
     * @param ContainerInterface $container
     * @param LoopInterface $loop
     * @param array $options
     * @return Client
     */
    public static function create(
        ContainerInterface $container,
        LoopInterface $loop,
        array $options = []
    ): Client {
        $container->get(EmitterInterface::class)->
            addListener(CommandLocatorEvent::NAME, function (CommandLocatorEvent $event) {
                $event->add(
                    __DIR__ . DIRECTORY_SEPARATOR . 'CommandBus' . DIRECTORY_SEPARATOR,
                    __NAMESPACE__ . '\CommandBus'
                );
            })
        ;

        $container->get(EmitterInterface::class)->
            addListener(ServiceLocatorEvent::NAME, function (ServiceLocatorEvent $event) {
                $event->add(
                    __DIR__ . DIRECTORY_SEPARATOR . 'Service' . DIRECTORY_SEPARATOR,
                    __NAMESPACE__ . '\Service'
                );
            })
        ;

        if (!isset($options[Options::DNS])) {
            $options[Options::DNS] = '8.8.8.8';
        }

        $resolver = (new ResolverFactory())->createCached($options[Options::DNS], $loop);
        $httpClient = (new HttpClientFactory())->create($loop, $resolver);

        return self::createFromReactHttpClient(
            $container,
            $httpClient,
            $resolver,
            $loop,
            $options
        );
    }

    /**
     * @param ContainerInterface $container
     * @param HttpClient $httpClient
     * @param Resolver $resolver
     * @param LoopInterface $loop
     * @param array $options
     * @return Client
     */
    public static function createFromReactHttpClient(
        ContainerInterface $container,
        HttpClient $httpClient,
        Resolver $resolver,
        LoopInterface $loop,
        array $options = []
    ): Client {
        return self::createFromBuzz(
            $container,
            $loop,
            (new Browser($loop, Sender::createFromLoopDns($loop, $resolver)))->withOptions([
                'streaming' => true,
            ]),
            $options
        );
    }

    /**
     * @param ContainerInterface $container
     * @param LoopInterface $loop
     * @param Browser $buzz
     * @param array $options
     * @return Client
     */
    public static function createFromBuzz(
        ContainerInterface $container,
        LoopInterface $loop,
        Browser $buzz,
        array $options = []
    ): Client {
        return new Client(
            $loop,
            $container,
            $buzz,
            $options
        );
    }
}
