<?php

namespace M6Web\Bundle\WSClientBundle\Adapter\Response;

use GuzzleHttp\Message\ResponseInterface;

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
    public function __construct(ResponseInterface $guzzleResponse)
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
        return (strtolower($this->getContentType()) == strtolower($type));
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return $this->getHeader('Content-Type');
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
        if ($header = $this->response->getHeader($header, true)) {
            return implode($glue, $header);
        }

        return $header;
    }

}
