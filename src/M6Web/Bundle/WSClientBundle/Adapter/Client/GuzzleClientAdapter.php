<?php

namespace M6Web\Bundle\WSClientBundle\Adapter\Client;

use Guzzle\Http\ClientInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Stopwatch\Stopwatch;
use M6Web\Bundle\WSClientBundle\Cache\CacheInterface;
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
            throw new \Exception('Class "' . $cacheAdapterClass . '" doesn\'t exist.');
        }

        if (!class_exists($cachePluginClass)) {
            throw new \Exception('Class "' . $cachePluginClass . '"  doesn\'t exist.');
        }

        $adapter = new $cacheAdapterClass($cacheService, $ttl);
        $cache = new $cachePluginClass($adapter);
        $this->client->addSubscriber($cache);

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
    public function get($uri, $headers = null)
    {
        if ($this->stopwatch) {
            $this->stopwatch->start('WSClientBundle::get');
        }

        $guzzleRequest = $this->client->get($uri, $headers, array(
            'query' => $this->getCacheQuery()
        ));

        if ($this->stopwatch) {
            $this->stopwatch->stop('WSClientBundle::get');
        }

        return new GuzzleRequestAdapter($guzzleRequest);
    }

    /**
     * {@inheritdoc}
     */
    public function post($uri, $headers = null, $body = null)
    {
        if ($this->stopwatch) {
            $this->stopwatch->start('WSClientBundle::post');
        }

        $guzzleRequest = $this->client->post($uri, $headers, $body, array(
            'query' => $this->getCacheQuery()
        ));

        if ($this->stopwatch) {
            $this->stopwatch->stop('WSClientBundle::post');
        }

        return new GuzzleRequestAdapter($guzzleRequest);
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
}
