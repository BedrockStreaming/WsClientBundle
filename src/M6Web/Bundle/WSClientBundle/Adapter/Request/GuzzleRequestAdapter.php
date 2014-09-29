<?php

namespace M6Web\Bundle\WSClientBundle\Adapter\Request;

use GuzzleHttp\Message\RequestInterface;
use M6Web\Bundle\WSClientBundle\Adapter\Response\GuzzleResponseAdapter;

/**
 * Adpater pour une requête Guzzle
 */
class GuzzleRequestAdapter implements RequestAdapterInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * Build a request
     *
     * @param RequestInterface $guzzleRequest Guzzle request at adapter
     *
     * @return \M6Web\Bundle\WSClientBundle\Adapter\Request\GuzzleRequestAdapter
     */
    public function __construct(RequestInterface $guzzleRequest)
    {
        $this->request = $guzzleRequest;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        return $this->request;
    }
}
