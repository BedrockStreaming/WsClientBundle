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
            if (is_array($header)) {
                return implode($glue, $header);
            }
        }

        return $header;
    }

    /**
     *  Magic method to the Response adapter
     *
     * @param string $name      method name
     * @param array  $arguments method arguments
     *
     * @throws Exception
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (!method_exists($this->response, $name)) {
            throw new \BadMethodCallException("Method ".$name." doesn't exist in ".get_class($this->response));
        }

        return call_user_func_array(array($this->response, $name), $arguments);
    }

}
