<?php

namespace M6Web\Bundle\WSClientBundle\Adapter\Request;

/**
 * Interface pour une requête du client de webservices
 */
interface RequestAdapterInterface
{
    /**
     * Get the request
     *
     * @return GuzzleHttp\Message\RequestInterface
     */
    public function getRequest();

}
