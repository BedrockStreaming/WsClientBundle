<?php

namespace M6Web\Bundle\WSClientBundle\test\units\Adapter\Client;

use Guzzle\Http\Message\RequestFactoryInterface;
use Guzzle\Parser\UriTemplate\UriTemplateInterface;
use Guzzle\Http\Curl\CurlMultiInterface;
use Guzzle\Http\ClientInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Faux client Guzzle utilisé pour les tests
 */
class FakeGuzzleClient implements ClientInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getAllEvents()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getEventDispatcher()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($eventName, array $context = array())
    {

    }

    /**
     * {@inheritdoc}
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function setSslVerification($certificateAuthority = true, $verifyPeer = true, $verifyHost = 2)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function setConfig($config)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getConfig($key = false)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultHeaders()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultHeaders($headers)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function expandTemplate($template, array $variables = null)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function setUriTemplate(UriTemplateInterface $uriTemplate)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getUriTemplate()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function createRequest($method = 'GET', $uri = null, $headers = null, $body = null, array $options = array())
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getBaseUrl($expand = true)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function setBaseUrl($url)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function setUserAgent($userAgent, $includeDefault = false)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function get($uri = null, $headers = null, $body = null)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function head($uri = null, $headers = null, array $options = array())
    {

    }

    /**
     * {@inheritdoc}
     */
    public function delete($uri = null, $headers = null, $body = null, array $options = array())
    {

    }

    /**
     * {@inheritdoc}
     */
    public function put($uri = null, $headers = null, $body = null, array $options = array())
    {

    }

    /**
     * {@inheritdoc}
     */
    public function patch($uri = null, $headers = null, $body = null, array $options = array())
    {

    }

    /**
     * {@inheritdoc}
     */
    public function post($uri = null, $headers = null, $postBody = null, array $options = array())
    {

    }

    /**
     * {@inheritdoc}
     */
    public function options($uri = null, array $options = array())
    {

    }

    /**
     * {@inheritdoc}
     */
    public function send($requests)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function setCurlMulti(CurlMultiInterface $curlMulti)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getCurlMulti()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function setRequestFactory(RequestFactoryInterface $factory)
    {

    }
}
