# Botika Socket HTTP PHP Library

PHP library for interacting with the Botika Socket HTTP API.

## Installation

You can get the Bpotika Socket PHP library via a composer package called `socket`. See <https://packagist.org/packages/botika/socket>

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
$username = 'USERNAME';
$password = 'PASSWORD';
$auth = new \Botika\Socket\Auth($username, $password);

// Options get from https://docs.guzzlephp.org/en/stable/request-options.html
$options = [];

// Initialize socket
$socket = new \Botika\Socket\Socket($auth, $options);
```

The second parameter is an `$options` array. The additional options get from <https://docs.guzzlephp.org/en/stable/request-options.html>

For example, by default calls will be made over HTTPS. To use plain
HTTP you can set verify to false:

```php
$options = [
  'base_uri' => 'https://socket.botika.online',
  'verify' => true
];
$socket = new \Botika\Socket\Socket($auth, $options);
```

## Logging configuration

The recommended approach of logging is to use a
[PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
compliant logger implementing `Psr\Log\LoggerInterface`. The `Pusher` object
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
$socket->trigger('my-channel', 'my_event', 'hello world');
```

### Multiple channels

```php
$pusher->trigger([ 'channel-1', 'channel-2' ], 'my_event', 'hello world');
```

### Asynchronous interface

Both `trigger` have asynchronous counterparts in `triggerAsync`. These functions return [Guzzle
promises](https://github.com/guzzle/promises) which can be chained
with `->then`:

```php
$promise = $socket->triggerAsync(['channel-1', 'channel-2'], 'my_event', 'hello world');
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

### Arrays

Arrays are automatically converted to JSON format:

```php
$array['name'] = 'joe';
$array['message_count'] = 23;

$socket->trigger('my_channel', 'my_event', $array);
```

The output of this will be:

```json
"{'name': 'joe', 'message_count': 23}"
```

## License

Copyright 2014, Pusher. Licensed under the MIT license:
<http://www.opensource.org/licenses/mit-license.php>

Copyright 2010, Squeeks. Licensed under the MIT license:
<http://www.opensource.org/licenses/mit-license.php>