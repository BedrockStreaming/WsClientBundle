<?php

namespace M6Web\Bundle\WSClientBundle\Adapter\Request;

/**
 * Interface pour une requête du client de webservices
 */
interface RequestAdapterInterface
{
    /**
     * Envoie la requête
     *
     * @return M6Web\Bundle\WSClientBundle\Adapter\Response\ResponseAdpaterInterface
     */
    public function send();

}
