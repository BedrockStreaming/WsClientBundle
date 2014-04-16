<?php

namespace M6Web\Bundle\WSClientBundle\Adapter\Client;

use Guzzle\Http\ClientInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Stopwatch\Stopwatch;
use M6Web\Bundle\WSClientBundle\Cache\CacheInterface;
use M6Web\Bundle\WSClientBundle\Cache\CacheResetterInterface;
use M6Web\Bundle\WSClientBundle\Adapter\Request\GuzzleRequestAdapter;

/**
 * Adpater pour un client de webaservices Guzzle
 */
class GuzzleClientAdapter implements ClientAdapterInterface
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var CacheResetterInterface
     */
    //protected $cacheResetter;

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
     * Construit un client
     *
     * @param ClientInterface $guzzleClient Client Guzzle à adapter
     *
     * @return \M6Web\Bundle\WSClientBundle\Adapter\Client\GuzzleClientAdapter
     */
    public function __construct(ClientInterface $guzzleClient)
    {
        $this->client = $guzzleClient;
        //$this->cacheResetter = null;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseUrl($url)
    {
        $this->client->setBaseUrl($url);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(array $config)
    {
        if (!isset($config['timeout'])) {
            $config['timeout'] = 1; // Par défaut
        }

        $curlConfig = array(
            CURLOPT_TIMEOUT => $config['timeout']
        );

        if (array_key_exists('followlocation', $config)) {
            $curlConfig[CURLOPT_FOLLOWLOCATION] = (bool) $config['followlocation'];
        }

        if (array_key_exists('maxredirs', $config)) {
            $curlConfig[CURLOPT_MAXREDIRS] = (int) $config['maxredirs'];
        }

        $this->client->setConfig(array('curl.options' => $curlConfig));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEventDispatcher(EventDispatcher $eventDispatcher)
    {
        $this->client->setEventDispatcher($eventDispatcher);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCache($ttl, CacheInterface $cacheService = null, $cacheAdapterClass = '', $cachePluginClass = '\Guzzle\Plugin\Cache\CachePlugin')
    {
        $this->cache = $cacheService;

        if (!class_exists($cacheAdapterClass)) {
            throw new \Exception('La classe cache adpater "' . $cacheAdapterClass . '" n\'est pas implémenté.');
        }

        if (!class_exists($cachePluginClass)) {
            throw new \Exception('La classe cache plugin "' . $cachePluginClass . '" n\'est pas implémenté.');
        }

        $adapter = new $cacheAdapterClass($cacheService, $ttl);
        $cache = new $cachePluginClass($adapter);
        $this->client->addSubscriber($cache);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
//    public function setCacheResetter(CacheResetterInterface $cacheResetter)
//    {
//        $this->cacheResetter = $cacheResetter;
//
//        return $this;
//    }

    /**
     * {@inheritdoc}
     */
    public function setCacheQueryParam($param)
    {
        $this->cacheQueryParam = $param;

        return $this;
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
    public function createRequest($method = 'GET', $uri = null, $headers = null, $body = null)
    {
        if ($this->stopwatch) {
            $this->stopwatch->start(
                sprintf('WSClientBundle::%s', strtolower($method))
            );
        }

        $options = array(
            'query' => $this->getCacheQuery()
        );

        $guzzleRequest = $this
            ->client
            ->createRequest($method, $uri, $headers, $body, $options);

        if (!is_null($this->requestTtl)) {
            $guzzleRequest
                ->getParams()
                ->set('cache.override_ttl', $this->requestTtl);
        }

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
        return $this->createRequest('GET', $uri, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function post($uri, $headers = null, $body = null)
    {
        return $this->createRequest('POST', $uri, $headers, $body);
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
}
