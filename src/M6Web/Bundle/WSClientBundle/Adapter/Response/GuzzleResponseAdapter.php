<?php

namespace M6Web\Bundle\WSClientBundle\Adapter\Response;

use Guzzle\Http\Message\Response;

/**
 * Adpater pour une réponse Guzzle
 */
class GuzzleResponseAdapter implements ResponseAdapterInterface
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * Construit une réponse
     *
     * @param Response $guzzleResponse Réponse Guzzle à adapter
     *
     * @return \M6Web\Bundle\WSClientBundle\Adapter\Response\GuzzleResponseAdapter
     */
    public function __construct(Response $guzzleResponse)
    {
        $this->response = $guzzleResponse;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return (string) $this->response->getBody();
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * {@inheritdoc}
     */
    public function isContentType($type)
    {
        return $this->response->isContentType($type);
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return $this->response->getContentType();
    }

    /**
     * retour le curl info http://www.php.net/manual/en/function.curl-getinfo.php
     * @param null $key
     *
     * @return string
     */
    public function getInfo($key = null)
    {
        return $this->response->getInfo($key);
    }

}
