<?php
declare(strict_types=1);

namespace ApiClients\Foundation\Transport;

use ApiClients\Foundation\Events\CommandLocatorEvent;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use League\Container\ContainerInterface;
use League\Event\EmitterInterface;
use React\Dns\Resolver\Resolver;
use React\EventLoop\Factory as LoopFactory;
use React\EventLoop\LoopInterface;
use React\HttpClient\Client as HttpClient;
use React\HttpClient\Factory as HttpClientFactory;
use React\Dns\Resolver\Factory as ResolverFactory;
use WyriHaximus\React\GuzzlePsr7\HttpClientAdapter;

class Factory
{
    /**
     * @param ContainerInterface $container
     * @param LoopInterface|null $loop
     * @param array $options
     * @return Client
     */
    public static function create(
        ContainerInterface $container,
        LoopInterface $loop = null,
        array $options = []
    ): Client {
        $container->get(EmitterInterface::class)->
            addListener(CommandLocatorEvent::NAME, function (CommandLocatorEvent $event) {
                $event->add(
                    dirname(__DIR__) . DIRECTORY_SEPARATOR . 'CommandBus' . DIRECTORY_SEPARATOR,
                    __NAMESPACE__ . '\CommandBus'
                );
            })
        ;

        if (!($loop instanceof LoopInterface)) {
            $loop = LoopFactory::create();
        }

        $container->share(LoopInterface::class, $loop);

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
     * @param LoopInterface|null $loop
     * @param array $options
     * @return Client
     */
    public static function createFromReactHttpClient(
        ContainerInterface $container,
        HttpClient $httpClient,
        Resolver $resolver,
        LoopInterface $loop = null,
        array $options = []
    ): Client {
        return self::createFromGuzzleClient(
            $container,
            $loop,
            new GuzzleClient(
                [
                    'handler' => HandlerStack::create(
                        new HttpClientAdapter(
                            $loop,
                            $httpClient,
                            $resolver
                        )
                    ),
                ]
            ),
            $options
        );
    }

    /**
     * @param ContainerInterface $container
     * @param LoopInterface $loop
     * @param GuzzleClient $guzzle
     * @param array $options
     * @return Client
     */
    public static function createFromGuzzleClient(
        ContainerInterface $container,
        LoopInterface $loop,
        GuzzleClient $guzzle,
        array $options = []
    ): Client {
        return new Client(
            $loop,
            $container,
            $guzzle,
            $options
        );
    }
}
