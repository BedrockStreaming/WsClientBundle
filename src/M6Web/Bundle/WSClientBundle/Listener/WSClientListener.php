<?php
namespace M6Web\Bundle\WSClientBundle\Listener;

use Symfony\Component\EventDispatcher\EventDispatcher;

use M6Web\Bundle\WSClientBundle\EventDispatcher\WSClientEvent;

/**
 * listener de request.complete de guzzle
 */
class WSClientListener
{
    const CLIENT_REQUESTCOMPLETE = 'wsclient.request_complete';

    /**
     * @var [type]
     */
    protected $dispatcher;

    /**
     * constructueur injectant le dispatch
     * @param EventDispatcher $dispatcher dispatcher
     */
    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * request.complete
     *
     * @param object $baseEvent event
     *
     * @return null
     */
    public function onRequestComplete($baseEvent)
    {
        $event = new WSClientEvent();
        $event->setCommand($baseEvent['request']->getMethod());
        $event->setTiming($baseEvent['response']->getInfo('total_time') * 1000); // Millisecondes
        $event->setStatusCode($baseEvent['response']->getStatusCode());
        $event->setUrl($baseEvent['request']->getUrl());
        $event->setContent($baseEvent['response']->getBody(true));

        $event->setKey($baseEvent['response']->getHeader('X-Guzzle-Cache'));
        if ($baseEvent['response']->getInfo('total_time') == null) {
            $event->setUseCache(true);
        } else {
            $event->setUseCache(false);
        }
        $this->dispatcher->dispatch('wsclient.command', $event);
        $this->dispatcher->dispatch(self::CLIENT_REQUESTCOMPLETE, $event);
    }
}
