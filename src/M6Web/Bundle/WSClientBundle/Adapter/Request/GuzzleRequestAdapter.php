<?php

namespace M6Web\Bundle\WSClientBundle\Adapter\Request;

use Guzzle\Http\Message\RequestInterface;
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
     * Construit une requête
     *
     * @param RequestInterface $guzzleRequest Requête Guzzle à adapter
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
    public function send()
    {
        $guzzleResponse = $this->request->send();

        return new GuzzleResponseAdapter($guzzleResponse);
    }
}
