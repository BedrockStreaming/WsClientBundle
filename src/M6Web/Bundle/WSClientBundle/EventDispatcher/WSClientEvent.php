<?php

namespace M6Web\Bundle\WSClientBundle\EventDispatcher;

use Symfony\Component\EventDispatcher\Event;

/**
 * Estat event
 */
class WSClientEvent extends Event
{
    /**
     * @var array
     */
    protected $command;

    /**
     * @var string
     */
    protected $responseCode;

    /**
     * @var integer
     */
    protected $executionTime;

    /**
     * @var array
     */
    protected $errors;

    /**
     * @var array
     */
    protected $arguments;

    /**
     * @var array
     */
    protected $url;

    /**
     * @var array
     */
    protected $useCache;

    /**
     * @var array
     */
    protected $content;

    /**
     * @var array
     */
    protected $key;

    /**
     * Setter du tableau de tags
     * @param string $command Methode
     *
     * @return WSClientEvent
     */
    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Getter de la méthode
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param float $executionTime Temps d'exécution en millisecondes
     *
     * @return WSClientEvent
     */
    public function setTiming($executionTime)
    {
        $this->executionTime = $executionTime;

        return $this;
    }

    /**
     * @return float
     */
    public function getTiming()
    {
        return $this->executionTime;
    }

    /**
     * @param string $responseCode Status code
     *
     * @return WSClientEvent
     */
    public function setStatusCode($responseCode)
    {
        $this->responseCode = $responseCode;

        return $this;
    }

    /**
     * Getter du status code
     *
     * @return string
     */
    public function getStatusCode()
    {
        return $this->responseCode;
    }

    /**
     * @param array $errors Tableau d'erreurs
     *
     * @return WSClientEvent
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Getter des erreurs
     *
     * @return array Tableau des erreurs
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * set the arguments
     * @param array $v argus
     */
    public function setArguments($v)
    {
        $this->arguments = $v;
    }

    /**
     * get the arguments
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * set the url
     * @param array $url argus
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * get the url
     * @return array
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * get the usecache
     * @return array
     */
    public function getUseCache()
    {
        return $this->useCache;
    }

    /**
     * set the usecache
     * @param array $bool argus
     */
    public function setUseCache($bool)
    {
        $this->useCache = $bool;
    }

    /**
     * get the usecache
     * @return array
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * set the usecache
     * @param string $content content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * get the key
     * @return array
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * set the key
     * @param string $key key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

}
