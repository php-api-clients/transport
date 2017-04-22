# API Client Transport for PHP 7.x

[![Build Status](https://travis-ci.org/php-api-clients/transport.svg?branch=master)](https://travis-ci.org/php-api-clients/transport)
[![Latest Stable Version](https://poser.pugx.org/api-clients/transport/v/stable.png)](https://packagist.org/packages/api-clients/transport)
[![Total Downloads](https://poser.pugx.org/api-clients/transport/downloads.png)](https://packagist.org/packages/api-clients/transport/stats)
[![Code Coverage](https://scrutinizer-ci.com/g/php-api-clients/transport/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/php-api-clients/transport/?branch=master)
[![License](https://poser.pugx.org/api-clients/transport/license.png)](https://packagist.org/packages/api-clients/transport)
[![PHP 7 ready](http://php7ready.timesplinter.ch/php-api-clients/transport/badge.svg)](https://appveyor-ci.org/php-api-clients/transport)

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
