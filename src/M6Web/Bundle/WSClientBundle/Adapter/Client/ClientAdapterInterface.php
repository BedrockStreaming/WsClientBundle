<?php

namespace M6Web\Bundle\WSClientBundle\Adapter\Client;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use M6Web\Bundle\WSClientBundle\Adapter\Request\RequestAdapterInterface;
use M6Web\Bundle\WSClientBundle\Cache\CacheInterface;

/**
 * Client interface
 */
interface ClientAdapterInterface
{
    /**
     * Define client cache
     * For options, please see : https://github.com/guzzle/cache-subscriber#creating-a-cachesubscriber
     *
     * $cache keys :
     * - cache_service : low level cache service (must implement M6Web\Bundle\WSClientBundle\Cache\CacheInterface)
     * - adapter_class : adapter class name (must implement \Doctrine\Common\Cache\Cache)
     * - storage_class : storage class name (must implement \GuzzleHttp\Subscriber\Cache\CacheStorageInterface)
     * - subscriber_class : subscriber class (must implement \GuzzleHttp\Subscriber\Cache\SubscriberInterface)
     * - can_cache_callable : a callable to determine if a request can be cached
     *
     * @param integer        $defaultTtl
     * @param boolean        $forceTtl
     * @param array          $cache
     * @param array          $options
     *
     * @return $this
     */
    public function setCache($defaultTtl, $forceTtl, array $cache, array $options);

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
     * @param EventDispatcherInterface $eventDispatcher dispatcher
     *
     * @return ClientAdapterInterface
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher);

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
     * @param array  $options
     *
     * @return RequestAdapterInterface
     */
    public function createRequest($method = 'GET', $uri = null, $options = null);

    /**
     * Send a request
     *
     * @param RequestAdapterInterface $request
     *
     * @return ResponseAdapterInterface
     */
    public function send(RequestAdapterInterface $request);
}
