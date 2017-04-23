<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport;

use ApiClients\Foundation\Middleware\Locator\Locator;
use Clue\React\Buzz\Browser;
use Clue\React\Buzz\Io\Sender;
use React\Dns\Resolver\Factory as ResolverFactory;
use React\Dns\Resolver\Resolver;
use React\EventLoop\LoopInterface;

class Factory
{
    /**
     * @param Locator $locator
     * @param LoopInterface $loop
     * @param array $options
     * @return Client
     */
    public static function create(
        Locator $locator,
        LoopInterface $loop,
        array $options = []
    ): Client {
        if (!isset($options[Options::DNS])) {
            $options[Options::DNS] = '8.8.8.8';
        }

        $resolver = (new ResolverFactory())->createCached($options[Options::DNS], $loop);

        return self::createFromResolver(
            $locator,
            $resolver,
            $loop,
            $options
        );
    }

    /**
     * @param Locator $locator
     * @param Resolver $resolver
     * @param LoopInterface $loop
     * @param array $options
     * @return Client
     */
    public static function createFromResolver(
        Locator $locator,
        Resolver $resolver,
        LoopInterface $loop,
        array $options = []
    ): Client {
        return self::createFromBuzz(
            $locator,
            $loop,
            (new Browser($loop, Sender::createFromLoopDns($loop, $resolver)))->withOptions([
                'streaming' => true,
            ]),
            $options
        );
    }

    /**
     * @param Locator $locator
     * @param LoopInterface $loop
     * @param Browser $buzz
     * @param array $options
     * @return Client
     */
    public static function createFromBuzz(
        Locator $locator,
        LoopInterface $loop,
        Browser $buzz,
        array $options = []
    ): Client {
        return new Client(
            $loop,
            $locator,
            $buzz,
            $options
        );
    }
}
