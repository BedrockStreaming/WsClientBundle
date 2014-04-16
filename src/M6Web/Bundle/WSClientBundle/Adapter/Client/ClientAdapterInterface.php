<?php

namespace M6Web\Bundle\WSClientBundle\Adapter\Client;

use M6Web\Bundle\WSClientBundle\Cache\CacheInterface;
use M6Web\Bundle\WSClientBundle\Cache\CacheResetterInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Interface pour un client de webservices
 */
interface ClientAdapterInterface
{
    /**
     * Définit la configuration du client
     *
     * @param array $config Configuration du client
     *
     * @return ClientAdapterInterface
     */
    public function setConfig(array $config);

    /**
     * Définit l'url de base pour le client
     *
     * @param string $url Url de base
     *
     * @return ClientAdapterInterface
     */
    public function setBaseUrl($url);

    /**
     * Définit le cache du client
     *
     * @param int    $ttl               Expiration du cache
     * @param mixed  $cacheService      Service de cache
     * @param string $cacheAdapterClass Classe adapter entre le service de cache et et le client
     *
     * @return ClientAdapterInterface
     */
    public function setCache($ttl, CacheInterface $cacheService = null, $cacheAdapterClass = '');

    /**
     * Définit le service de purge du cache
     *
     * @param CacheResetterInterface $cacheResetter Service de purge du cache
     *
     * @return ClientAdapterInterface
     */
    //public function setCacheResetter(CacheResetterInterface $cacheResetter);

    /**
     * Définit le paramètre à ajouter à l'url de la requête pour vider le cache
     *
     * @param string $param Paramètre
     *
     * @return ClientAdapterInterface
     */
    public function setCacheQueryParam($param);

    /**
     * Permet de savoir si le client doit purger le cache
     *
     * @return boolean
     */
    public function shouldResetCache();

    /**
     * Définit le dispatcheur d'évènement
     *
     * @param EventDispatcher $eventDispatcher Dispatcheur d'évènement
     *
     * @return ClientAdapterInterface
     */
    public function setEventDispatcher(EventDispatcher $eventDispatcher);

    /**
     * Définit le stopwatcher (timeline de la debug toolbar)
     *
     * @param Stopwatch $stopwatch Stopwatcher
     *
     * @return ClientAdapterInterface
     */
    public function setStopWatch(Stopwatch $stopwatch);

    /**
     * Lance une requête de type GET
     *
     * @param mixed $uri     Uri de la requete
     * @param array $headers Header de la requete
     *
     * @return Request
     */
    public function get($uri, $headers = null);

    /**
     * Lance une requête de type POST
     *
     * @param string $uri     Uri de la requete
     * @param array  $headers Header de la requete
     * @param string $body    Body de la requete
     *
     * @return Request
     */
    public function post($uri, $headers = null, $body = null);

    /**
     * Définit le TTL pour toutes les requêtes.
     * Surcharge le ttl calculé automatiquement si non null
     *
     * @param null|int $ttl Time To Live
     *
     * @return ClientAdapterInterface
     */
    public function setRequestTtl($ttl);

    /**
     * Créé une requête
     *
     * @param string $method
     * @param string $uri
     * @param array  $headers
     * @param string $body
     *
     * @return RequestAdapterInterface
     */
    public function createRequest($method = 'GET', $uri = null, $headers = null, $body = null);
}
