<?php

namespace Botika\Socket;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LogLevel;

class Socket
{
    use LoggerAwareTrait;

    /**
     * Http Client
     * 
     * @var \GuzzleHttp\Client
     */
    protected Client $httpClient;

    /**
     * Http Client Default Config
     * 
     * @var array
     */
    protected array $httpClientDefaultConfig = [
        'connect_timeout' => 10,
        'http_errors' => false,
        'timeout' => 30,
    ];

    /**
     * Constructor.
     * 
     * @param  \Botika\Socket\Auth  $auth
     * @param  array  $config
     */
    public function __construct(Auth $auth, array $config = [])
    {
        $this->httpClient = new Client(
            array_merge($config, $this->httpClientDefaultConfig, [
                'auth' => $auth->toArray()
            ])
        );
    }

    /**
     * Make Json Request
     * 
     * @param  string|array  $channels
     * @param  string  $event
     * @param  array|null  $data
     * @return array
     */
    protected function makeJsonRequest(string|array $channels, string $event, array|string|null $data): array
    {
        if (is_string($channels)) {
            $channels = [$channels];
        }

        return [
            'channels' => $channels,
            'event' => $event,
            'data' => $data,
        ];
    }

    /**
     * Log a string.
     *
     * @param string  $message  The message to log
     * @param array|\Exception  $context [optional] Any extraneous information that does not fit well in a string.
     * @param string  $level  [optional] Importance of log message, highly recommended to use Psr\Log\LogLevel::{level}
     */
    protected function log(string $message, array $context = [], string $level = LogLevel::DEBUG): void
    {
        if (is_null($this->logger)) {
            return;
        }

        $this->logger->log($level, $message, $context);
    }

    /**
     * Trigger an event by providing event name and payload.
     * Optionally provide a socket ID to exclude a client (most likely the sender).
     * 
     * @param  string|array  $channels
     * @param  string  $event
     * @param  array|null  $data
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function trigger(string|array $channels, string $event, array|null $data): ResponseInterface
    {
        $this->log('Botika Socket Trigger', $this->makeJsonRequest($channels, $event, $data));
        
        return $this->httpClient->post('/events', [
            'json' => $this->makeJsonRequest($channels, $event, $data),
        ]);
    }

    /**
     * Asynchronously trigger an event by providing event name and payload.
     * Optionally provide a socket ID to exclude a client (most likely the sender).
     * 
     *  @param  string|array  $channels
     * @param  string  $event
     * @param  array|null  $data
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function triggerAsync(string|array $channels, string $event, array|null $data): PromiseInterface
    {
        $this->log('Botika Socket Trigger Asynchronously', $this->makeJsonRequest($channels, $event, $data));

        return $this->httpClient->postAsync('/events', [
            'json' => $this->makeJsonRequest($channels, $event, $data),
        ]);
    }
}