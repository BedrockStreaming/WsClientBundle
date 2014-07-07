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
     * Build a response
     *
     * @param Response $guzzleResponse Guzzle response at adapter
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
     * retour the curl info http://www.php.net/manual/en/function.curl-getinfo.php
     *
     * @param null $key
     *
     * @return string
     */
    public function getInfo($key = null)
    {
        return $this->response->getInfo($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($header)
    {
        return $this->response->getHeader($header);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->response->getHeaders();
    }

    /**
     * return a scalar value of \Guzzle\Http\Message\Header type
     *
     * @param string $header Header name
     * @param string $glue   Separator (default='')
     *
     * @return string
     */
    public function getHeaderValue($header, $glue = '')
    {
        // $header is \Guzzle\Http\Message\Header
        if ($header = $this->getHeader($header)) {
            return implode($glue, $header->toArray());
        }

        return $header;
    }

}
