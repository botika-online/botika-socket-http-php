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
     * Auth
     * 
     * @var \Botika\Socket\Auth
     */
    protected Auth $auth;

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
        'verify' => false,
    ];

    /**
     * Constructor.
     * 
     * @param  string  $baseURL
     * @param  \Botika\Socket\Auth  $auth
     */
    public function __construct(string $baseURL, Auth $auth)
    {
        $this->auth = $auth;
        $this->httpClient = new Client(array_merge($this->httpClientDefaultConfig, [
            'base_uri' => $baseURL
        ]));
    }

    /**
     * Make Json Request
     * 
     * @param  string|array  $channels
     * @param  string  $event
     * @param  array|null  $data
     * @return array
     */
    protected function makeJsonRequest($channels, string $event, $data): array
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
     * @param  array|string|null  $data
     * @param  array  $config
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function trigger($channels, string $event, $data = null, array $config = []): ResponseInterface
    {
        $this->log('Botika Socket Trigger', $this->makeJsonRequest($channels, $event, $data));
        
        return $this->httpClient->post('/events', array_merge($config, [
            'auth' => $this->auth->toArray(),
            'json' => $this->makeJsonRequest($channels, $event, $data),
        ]));
    }

    /**
     * Asynchronously trigger an event by providing event name and payload.
     * Optionally provide a socket ID to exclude a client (most likely the sender).
     * 
     * @param  string|array  $channels
     * @param  string  $event
     * @param  array|string|null  $data
     * @param  array  $config
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function triggerAsync($channels, string $event, $data = null, array $config = []): PromiseInterface
    {
        $this->log('Botika Socket Trigger Asynchronously', $this->makeJsonRequest($channels, $event, $data));

        return $this->httpClient->postAsync('/events', array_merge($config, [
            'auth' => $this->auth->toArray(),
            'json' => $this->makeJsonRequest($channels, $event, $data),
        ]));
    }
}