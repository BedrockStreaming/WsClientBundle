<?php

namespace M6Web\Bundle\WSClientBundle\Adapter\Response;

/**
 * Interface pour une réponse du client de webservices
 */
interface ResponseAdapterInterface
{
    /**
     * Return body response
     *
     * @return string
     */
    public function getBody();

    /**
     * Return the response status
     *
     * @return int
     */
    public function getStatusCode();

    /**
     * Return content type
     *
     * @param string $type Content type to check against
     *
     * @return bool
     */
    public function isContentType($type);

    /**
     * Return a content type
     *
     * @return string
     */
    public function getContentType();

    /**
     * Return a specific header response
     *
     * @param string $header Header name
     *
     * @return string
     */
    public function getHeader($header);

    /**
     * Return headers response
     *
     * @return array
     */
    public function getHeaders();

    /**
     * return a scalar value of \Guzzle\Http\Message\Header type
     *
     * @param string $header Header name
     * @param string $glue   Separator (default='')
     *
     * @return string
     */
    public function getHeaderValue($header, $glue = '');

}
