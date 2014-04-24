<?php

namespace M6Web\Bundle\WSClientBundle\Adapter\Client;

use M6Web\Bundle\WSClientBundle\Cache\CacheInterface;
use M6Web\Bundle\WSClientBundle\Cache\CacheResetterInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Client interface
 */
interface ClientAdapterInterface
{
    /**
     * Define client configuration
     *
     * @param array $config Client configuration
     *
     * @return ClientAdapterInterface
     */
    public function setConfig(array $config);

    /**
     * Define client base URL
     *
     * @param string $url Base URL
     *
     * @return ClientAdapterInterface
     */
    public function setBaseUrl($url);

    /**
     * Getclient base URL
     *
     * @return string
     */
    public function getBaseUrl();

    /**
     * Define client cache
     *
     * @param int    $ttl               Cache expiration time
     * @param mixed  $cacheService      Cache service
     * @param string $cacheAdapterClass Class adapter between cache service and client
     *
     * @return ClientAdapterInterface
     */
    public function setCache($ttl, CacheInterface $cacheService = null, $cacheAdapterClass = '');

    /**
     * Define the query parameter to add to clear cache
     *
     * @param string $param Parameter
     *
     * @return ClientAdapterInterface
     */
    public function setCacheQueryParam($param);

    /**
     * Return TRUE if client has to clear the cache
     *
     * @return boolean
     */
    public function shouldResetCache();

    /**
     * Define event dispatcher
     *
     * @param EventDispatcher $eventDispatcher dispatcher
     *
     * @return ClientAdapterInterface
     */
    public function setEventDispatcher(EventDispatcher $eventDispatcher);

    /**
     * Define stopwatcher (debug toolbar timeline)
     *
     * @param Stopwatch $stopwatch Stopwatcher
     *
     * @return ClientAdapterInterface
     */
    public function setStopWatch(Stopwatch $stopwatch);

    /**
     * Crete GET request
     *
     * @param mixed $uri     Uri
     * @param array $headers Headers
     *
     * @return Request
     */
    public function get($uri, $headers = null);

    /**
     * Create POST request
     *
     * @param string $uri     Uri
     * @param array  $headers Headers
     * @param string $body    Body
     *
     * @return Request
     */
    public function post($uri, $headers = null, $body = null);

    /**
     * Define the TTL of all request.
     * Override the calculated request TTL if not null.
     *
     * @param null|int $ttl Time To Live
     *
     * @return ClientAdapterInterface
     */
    public function setRequestTtl($ttl);

    /**
     * Create a request
     *
     * @param string $method
     * @param string $uri
     * @param array  $headers
     * @param string $body
     *
     * @return RequestAdapterInterface
     */
    public function createRequest($method = 'GET', $uri = null, $headers = null, $body = null);
}
