<?php

namespace M6Web\Bundle\WSClientBundle\Adapter\Client;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use M6Web\Bundle\WSClientBundle\Adapter\Request\GuzzleRequestAdapter;
use M6Web\Bundle\WSClientBundle\Adapter\Response\GuzzleResponseAdapter;
use M6Web\Bundle\WSClientBundle\Adapter\Response\ResponseAdapterInterface;
use M6Web\Bundle\WSClientBundle\Adapter\Request\RequestAdapterInterface;
use Doctrine\Common\Cache\Cache;
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
     * @param integer        $defaultTtl
     * @param boolean        $forceTtl
     * @param CacheInterface $cacheService
     * @param string         $cacheAdapterClass
     * @param string         $cacheStorageClass
     * @param array          $options
     * @param array          $canCacheClass
     * @param string         $cacheSubscriberClass
     *
     * @return $this
     */
    public function setCache(
        $defaultTtl, $forceTtl,
        CacheInterface $cacheService,
        $cacheAdapterClass,
        $cacheStorageClass,
        array $options,
        array $canCacheClass,
        $cacheSubscriberClass
    );

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
