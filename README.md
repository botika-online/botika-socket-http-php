# Botika Socket HTTP PHP Library

PHP library for interacting with the Botika Socket HTTP API.

## Installation

You can get the Botika Socket PHP library via a composer package called `socket`. See <https://packagist.org/packages/botika/socket>

```bash
composer require botika/socket
```

Or add to `composer.json`:

```json
"require": {
    "botika/socket": "^1.0"
}
```

then run `composer update`.

## Supported platforms

* PHP - supports PHP versions 7.0, and above.

## Botika Socket constructor

Use the credentials from your Botika Socket application to create a new `Botika\Socket` instance.

```php
$baseURL = 'https://socket.example.com';
$username = 'USERNAME';
$password = 'PASSWORD';
$auth = new \Botika\Socket\Auth($username, $password);

// Initialize socket
$socket = new \Botika\Socket\Socket($baseURL, $auth);
```

## Logging configuration

The recommended approach of logging is to use a
[PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
compliant logger implementing `Psr\Log\LoggerInterface`. The `Socket` object
implements `Psr\Log\LoggerAwareInterface`, meaning you call
`setLogger(LoggerInterface $logger)` to set the logger instance.

```php
// where $logger implements `LoggerInterface`

$socket->setLogger($logger);
```

## Publishing/Triggering events

To trigger an event on one or more channels use the `trigger` function.

### A single channel

```php
// Options get from https://docs.guzzlephp.org/en/stable/request-options.html
$options = [];
$socket->trigger('my-channel', 'my_event', 'hello world', $options);
```

### Multiple channels

```php
// Options get from https://docs.guzzlephp.org/en/stable/request-options.html
$options = [];
$socket->trigger([ 'channel-1', 'channel-2' ], 'my_event', 'hello world', $options);
```

### Asynchronous interface

Both `trigger` have asynchronous counterparts in `triggerAsync`. These functions return [Guzzle
promises](https://github.com/guzzle/promises) which can be chained
with `->then`:

```php
// Options get from https://docs.guzzlephp.org/en/stable/request-options.html
$options = [];
$promise = $socket->triggerAsync(['channel-1', 'channel-2'], 'my_event', 'hello world', $options);
$promise->then(
    function (ResponseInterface $res) {
        echo $res->getStatusCode() . "\n";
    },
    function (RequestException $e) {
        echo $e->getMessage() . "\n";
        echo $e->getRequest()->getMethod();
    }
);
$promise->wait();
```