# API Client Transport for PHP 7.x

[![Build Status](https://travis-ci.org/php-api-clients/transport.svg?branch=master)](https://travis-ci.org/php-api-clients/transport)
[![Latest Stable Version](https://poser.pugx.org/api-clients/transport/v/stable.png)](https://packagist.org/packages/api-clients/transport)
[![Total Downloads](https://poser.pugx.org/api-clients/transport/downloads.png)](https://packagist.org/packages/api-clients/transport/stats)
[![Code Coverage](https://scrutinizer-ci.com/g/php-api-clients/transport/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/php-api-clients/transport/?branch=master)
[![License](https://poser.pugx.org/api-clients/transport/license.png)](https://packagist.org/packages/api-clients/transport)
[![PHP 7 ready](http://php7ready.timesplinter.ch/php-api-clients/transport/badge.svg)](https://appveyor-ci.org/php-api-clients/transport)

In a nutshell this package is a wrapper around `clue/buzz-react` adding [middleware](https://github.com/php-api-clients/middleware).

# Install

To install via [Composer](http://getcomposer.org/), use the command below, it will automatically detect the latest version and bind it with `^`.

```
composer require api-clients/transport
```

# Usage

Creating the client can be done using the factory:

```php
$locator = new MiddlewareLocator(); // A concrete class implementing the middleware locator interface 
$loop = EventLoopFactory::create(); // The event loop
$options = []; // Client options, as described below
$client = Factory::create($locator, $loop, $options);
```

Next you can make PSR-7 request:

```php
$request = new PsrRequest(); // 
$requestOptions = []; // Options such as middleware settings
$client->request($request, $requestOptions)->done(function (ResponseInterface $response) {
    // Handle the response
});
```

# Middleware

Middlewares are passed into the client with the options argument. In this example the [`api-clients/middleware-delay`](https://github.com/php-api-clients/middleware-delay) is used. Adding middlewares to the client is simple, add an array to `$options` with `Options::MIDDLEWARE` as index cosisting of middleware class names. Optionally you can pass options for the middleware through the `$options` array. Simply add a new array inside the array with the middlware class name as index and pass the desired options into it.

```php
$options = [
    Options::DEFAULT_REQUEST_OPTIONS => [
        \ApiClients\Middleware\Delay\DelayMiddleware::class => [
            \ApiClients\Middleware\Delay\Options::DELAY => 3,
        ],
    ],
    Options::MIDDLEWARE => [
        \ApiClients\Middleware\Delay\DelayMiddleware::class,
    ],
];

$client = new Client($loop, $container, $browser, $options);
```

Middleware options can be changed per request, this specific request only will have a delay of 5 seconds instead of the default 3:

```php
$requestOptions = [
    \ApiClients\Middleware\Delay\DelayMiddleware::class => [
        \ApiClients\Middleware\Delay\Options::DELAY => 5,
    ],
];
$client->request($request, $requestOptions);
```

# Options

## Options::DNS

DNS server to use resolving hostnames, defaults to `8.8.8.8`. 

## Options::SCHEMA

Schema part of the URI, defaults to `https`. 

## Options::HOST

Host part of the URI, required. 

## Options::PORT

Port part of the URI, optional. 

## Options::PATH

Path part of the URI, defaults to `/`. 

## Options::HEADERS

Key value array with headers, defaults to `[]`.

## Options::MIDDLEWARE

Array with middleware class names, for example `[MiddlewareOne::class, MiddlewareTwo::class]`, defaults to `[]`.

## Options::DEFAULT_REQUEST_OPTIONS

Set of default request options, mainly useful for middlewares needed for all requests, defaults to `[]`.

# License

The MIT License (MIT)

Copyright (c) 2017 Cees-Jan Kiewiet

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
