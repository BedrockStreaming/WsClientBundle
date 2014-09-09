<?php

namespace M6Web\Bundle\WSClientBundle\Adapter\Client;

use GuzzleHttp\ClientInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use M6Web\Bundle\WSClientBundle\Adapter\Request\GuzzleRequestAdapter;
use M6Web\Bundle\WSClientBundle\Adapter\Response\GuzzleResponseAdapter;
use M6Web\Bundle\WSClientBundle\Adapter\Response\ResponseAdapterInterface;
use M6Web\Bundle\WSClientBundle\Adapter\Request\RequestAdapterInterface;
use M6Web\Bundle\WSClientBundle\Cache\CacheInterface;
use Doctrine\Common\Cache\Cache;

/**
 * Adapter form Guzzle webservices client
 */
class GuzzleClientAdapter implements ClientAdapterInterface
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var string
     */
    protected $cacheQueryParam = null;

    /**
     * @var Stopwatch
     */
    protected $stopwatch = null;

    /**
     * @var integer
     */
    protected $requestTtl;

    /**
     * @var boolean
     */
    protected $throwExceptionOnHttpError;

    /**
     * Build a client
     *
     * @param ClientInterface $guzzleClient Guzzle client at adapter
     *
     * @return \M6Web\Bundle\WSClientBundle\Adapter\Client\GuzzleClientAdapter
     */
    public function __construct(ClientInterface $guzzleClient)
    {
        $this->client                    = $guzzleClient;
        $this->throwExceptionOnHttpError = false;
    }

    /**
     * {@inheritdoc}
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCache(
        $defaultTtl, $forceTtl,
        CacheInterface $cacheService = null,
        $cacheAdapterClass = '',
        $cacheStorageClass = '',
        array $options = [],
        array $canCacheClass = null,
        $cacheSubscriberClass = '\GuzzleHttp\Subscriber\Cache\CacheSubscriber'
    )
    {
        $this->cache = $cacheService;

        // Cache storage (Doctrine cache)
        if (!class_exists($cacheAdapterClass) ||
            !is_subclass_of($cacheAdapterClass, '\Doctrine\Common\Cache\Cache', true)) {
            throw new \Exception(
                'Class "' . $cacheAdapterClass . '" doesn\'t exists or doesn\'t implement Doctrine\Common\Cache\Cache.'
            );
        }

        // Cache storage (Doctrine cache)
        if (!class_exists($cacheStorageClass) ||
            !is_subclass_of($cacheStorageClass, '\GuzzleHttp\Subscriber\Cache\CacheStorageInterface', true)) {
            throw new \Exception(
                'Class "' . $cacheStorageClass . '" doesn\'t exists or doesn\'t implement GuzzleHttp\Subscriber\Cache\CacheStorageInterface.'
            );
        }

        // Cache subscriber
        if (!class_exists($cacheSubscriberClass) ||
            !is_subclass_of($cacheSubscriberClass, '\GuzzleHttp\Event\SubscriberInterface', true)) {
            throw new \Exception('Class "' . $cacheSubscriberClass . '" doesn\'t exists.');
        }

        $options['storage'] = new $cacheStorageClass(new $cacheAdapterClass($cacheService, $defaultTtl, $forceTtl));
        $options['can_cache'] = $canCacheClass;

        // Attach the cache to our client
        $cacheSubscriberClass::attach($this->client, $options);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCacheQueryParam($param)
    {
        $this->cacheQueryParam = $param;

        return $this;
    }

    /**
     * Set the CacheResetter
     *
     * @param CacheResetterInterface $cacheResetter
     */
    public function setCacheResetter($cacheResetter)
    {
        if (!is_null($this->cache)) {
            $this->cache->setCacheResetter($cacheResetter);
        }
    }

    /**
     * @return {@inheritdoc}
     */
    public function shouldResetCache()
    {
        if (!is_null($this->cache)) {
            return $this->cache->shouldResetCache();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setStopWatch(Stopwatch $stopwatch = null)
    {
        $this->stopwatch = $stopwatch;
    }

    /**
     * {@inheritdoc}
     */
    public function createRequest($method = 'GET', $uri = null, $options = [])
    {
        if ($this->stopwatch) {
            $this->stopwatch->start(
                sprintf('WSClientBundle::%s', strtolower($method))
            );
        }

        $guzzleRequest = $this
            ->client
            ->createRequest($method, $uri, $options);

        if ($this->stopwatch) {
            $this->stopwatch->stop(
                sprintf('WSClientBundle::%s', strtolower($method))
            );
        }

        return new GuzzleRequestAdapter($guzzleRequest);
    }

    /**
     * {@inheritdoc}
     */
    public function get($uri, $headers = null)
    {
        return new GuzzleResponseAdapter(
            $this->client->get($uri, ['headers' => $headers ? : []])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function post($uri, $headers = null, $body = null)
    {
        return new GuzzleResponseAdapter(
            $this->client->post($uri, [
                'headers' => $headers ? : [],
                'body' => $body
            ])
        );
    }

    /**
     * Gets the query for cache clearing of the request if necessary
     *
     * @return array
     */
    protected function getCacheQuery()
    {
        if ($this->shouldResetCache() && $this->cacheQueryParam) {
            return array($this->cacheQueryParam => 1);
        }

        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function setRequestTtl($ttl)
    {
        $this->requestTtl = $ttl;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function send(RequestAdapterInterface $request)
    {
        $guzzleResponse = $this->client->send($request->getRequest());

        return new GuzzleResponseAdapter($guzzleResponse);
    }
}
