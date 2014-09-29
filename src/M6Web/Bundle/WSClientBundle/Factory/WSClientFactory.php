<?php

namespace M6Web\Bundle\WSClientBundle\Factory;

use Symfony\Component\DependencyInjection\ContainerInterface;
use M6Web\Bundle\WSClientBundle\Adapter\Client;

/**
 * Génerate a guzzle client to call webservices
 */
class WSClientFactory
{


    /**
     * Factory to get a ws client
     * @param ContainerInterface $container          Container
     * @param string             $baseUrl            Base url
     * @param array              $config             Client configuration
     * @param string             $clientAdapterClass Client adpater class - Default is Guzzle
     *
     * @throws \Exception
     * @return M6Web\Bundle\WSClientBundle\Adapter\Client\ClientAdapterInterface
     */
    public static function getClient(ContainerInterface $container, $baseUrl = '', $config = array(), $clientAdapterClass = '')
    {
        if (!$baseUrl && $container->isScopeActive('request')) {
            $baseUrl =  'http://'.$container->get('request')->getHttpHost();
        }

        switch (strtolower($clientAdapterClass)) {
            case '':
            case 'guzzle':
                $guzzleClient = new \GuzzleHttp\Client([
                    'base_url' => $baseUrl,
                    'defaults' => $config
                ]);

                $client = new Client\GuzzleClientAdapter($guzzleClient);
                break;
            default:
                if (!class_exists($clientAdapterClass)) {
                    throw new \InvalidArgumentException('Client adapter class "' . $clientAdapterClass . '" doesn\'t exists.');
                }
                $client = new $clientAdapterClass($baseUrl, $config);
                break;
        }

        $client->setEventDispatcher($container->get('event_dispatcher'));

        return $client;
    }
}
