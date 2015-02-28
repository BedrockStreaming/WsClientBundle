<?php

namespace M6Web\Bundle\WSClientBundle\Adapter\Client;

use GuzzleHttp\ClientInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use M6Web\Bundle\WSClientBundle\Adapter\Request\GuzzleRequestAdapter;
use M6Web\Bundle\WSClientBundle\Adapter\Response\GuzzleResponseAdapter;
use M6Web\Bundle\WSClientBundle\Adapter\Request\RequestAdapterInterface;
use M6Web\Bundle\WSClientBundle\Cache\CacheInterface;

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
    public function setCache($defaultTtl, $forceTtl, array $cache, array $options = [])
    {
        // Cache service
        $this->cache = empty($cache['cache_service']) ? null : $cache['cache_service'];
        if (!is_subclass_of($this->cache, '\M6Web\Bundle\WSClientBundle\Cache\CacheInterface')) {
            throw new Exception('Cache service must implement M6Web\Bundle\WSClientBundle\Cache\CacheInterface');
        }

        // Cache storage (Doctrine cache)
        $cacheAdapterClass = empty($cache['adapter_class']) ? '' : $cache['adapter_class'];
        if (!class_exists($cacheAdapterClass) ||
            !is_subclass_of($cacheAdapterClass, '\Doctrine\Common\Cache\Cache', true)) {
            throw new Exception(
                'Class "' . $cacheAdapterClass . '" doesn\'t exists or doesn\'t implement Doctrine\Common\Cache\Cache.'
            );
        }

        // Cache storage (Doctrine cache)
        $cacheStorageClass = empty($cache['storage_class']) ? 'GuzzleHttp\Subscriber\Cache\CacheStorage' : $cache['storage_class'];
        if (!class_exists($cacheStorageClass) ||
            !is_subclass_of($cacheStorageClass, '\GuzzleHttp\Subscriber\Cache\CacheStorageInterface', true)) {
            throw new Exception(
                'Class "' . $cacheStorageClass . '" doesn\'t exists or doesn\'t implement GuzzleHttp\Subscriber\Cache\CacheStorageInterface.'
            );
        }

        // Cache subscriber
        $cacheSubscriberClass = empty($cache['subscriber_class']) ? 'GuzzleHttp\Subscriber\Cache\CacheSubscriber' : $cache['subscriber_class'];
        if (!class_exists($cacheSubscriberClass) ||
            !is_subclass_of($cacheSubscriberClass, '\GuzzleHttp\Event\SubscriberInterface', true)) {
            throw new Exception('Class "' . $cacheSubscriberClass . '" doesn\'t exists or doesn\'t implement GuzzleHttp\Event\SubscriberInterface.');
        }

        // Callable can cache
        $canCacheCallable = empty($cache['can_cache_callable']) ? ['GuzzleHttp\Subscriber\Cache\Utils', 'canCacheRequest'] : $cache['can_cache_callable'];
        if (!is_callable($canCacheCallable)) {
            throw new Exception('can_cache_callable must be a callabe');
        }

        $options['storage'] = new $cacheStorageClass(new $cacheAdapterClass($this->cache, $defaultTtl, $forceTtl));
        $options['can_cache'] = $canCacheCallable;

        // Attach the cache to our client
        $cacheSubscriberClass::attach($this->client, $options);

        return $this;
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
     * {@inheritdoc}
     */
    public function put($uri, $headers, $body)
    {
        return new GuzzleResponseAdapter(
            $this->client->put($uri, [
                'headers' => $headers ? : [],
                'body' => $body
            ])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function patch($uri, $headers, $body)
    {
        return new GuzzleResponseAdapter(
            $this->client->patch($uri, [
                'headers' => $headers ? : [],
                'body' => $body
            ])
        );
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
