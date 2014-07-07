<?php

namespace M6Web\Bundle\WSClientBundle\Adapter\Response;

/**
 * Interface pour une réponse du client de webservices
 */
interface ResponseAdapterInterface
{
    /**
     * Retourne le corps de la réponse
     *
     * @return string
     */
    public function getBody();

    /**
     * Retourne le statut de la réponse
     *
     * @return int
     */
    public function getStatusCode();

    /**
     * retourne l'encoding du Content
     * @param string $type Content type to check against
     *
     * @return bool
     */
    public function isContentType($type);

    /**
     * retourne le content type
     * @return string
     */
    public function getContentType();

    /**
     * retourne un header de la réponse
     * @param string $header Header name
     *
     * @return string
     */
    public function getHeader($header);

    /**
     * retourne les headers de la réponse
     * @return array
     */
    public function getHeaders();

}
