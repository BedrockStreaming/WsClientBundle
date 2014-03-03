<?php
namespace M6Web\Bundle\WSClientBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use M6Web\Bundle\WSClientBundle\EventDispatcher as WSEventDispatcher;
/**
 * Handle datacollector for Wsclient
 */
class WSClientDataCollector extends DataCollector
{
    private $data;

    /**
     * Construct the data collector
     */
    public function __construct()
    {
        $this->data['commands'] = array();
    }

    /**
     * Collect the data
     * @param Request    $request   The request object
     * @param Response   $response  The response object
     * @param \Exception $exception An exception
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
    }

    /**
     * Listen for wsclient command event
     * @param \M6Web\Bundle\WSClientBundle\EventDispatcher\WSClientEvent|object $event The event object
     */
    public function onWSClientCommand(WSEventDispatcher\WSClientEvent $event)
    {
        //$command = $event->getCommand();
        //$arguments = $event->getArguments();
        $this->data['commands'][] = array(
            'command'   => $event->getCommand(),
            'arguments' => $event->getArguments(),
            'url' => $event->getUrl(),
            'cache' => $event->getUseCache(),
            'content' => $event->getContent(),
            'key' => $event->getKey(),
            'statusCode' => $event->getStatusCode(),
            'executiontime' => $event->getTiming()
        );
    }

    /**
     * Return command list and number of times they were called
     * @return array The command list and number of times called
     */
    public function getCommands()
    {
        return $this->data['commands'];
    }

    /**
     * Return the name of the collector
     * @return string data collector name
     */
    public function getName()
    {
        return 'wsclient';
    }

    /**
     * temps total d'exec des commandes
     * @return float
     */
    public function getTotalExecutionTime()
    {
        $ret = 0;
        foreach ($this->data['commands'] as $command) {
            $ret += $command['executiontime'];
        }

        return $ret;
    }

    /**
     * temps moyen d'exec
     * @return float
     */
    public function getAvgExecutionTime()
    {
        return ($this->getTotalExecutionTime()) ? ($this->getTotalExecutionTime() / count($this->data['commands']) ) : 0;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
