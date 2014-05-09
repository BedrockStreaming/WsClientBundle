<?php
namespace M6Web\Bundle\WSClientBundle\Tests\Units\Adapter\Client;

use mageekguy\atoum\test;
use M6Web\Bundle\WSClientBundle\Adapter\Client\GuzzleClientAdapter as Base;
use Guzzle\Http\Client;

/**
 * GuzzleClientAdapter test class
 *
 * @maxChildrenNumber 1
 */
class GuzzleClientAdapter extends test
{
     /**
      * Get a mocked GuzzleClientAdapter who dont really use Guzzle
      *
      * @param int    $statusCode Statut de la réponse retrounée
      * @param string $return     Contenu de la réponse retournée
      *
      * @return M6Web\Bundle\WSClientBundle\Adapter\Client\GuzzleClientAdapter
     */
    protected function buildMockWsClient($statusCode=200, $return=null)
    {
        $guzzleClient = new \mock\Guzzle\Http\Client();

        $guzzleResponse = new \mock\Guzzle\Http\Message\Response($statusCode);
        $guzzleResponse->getMockController()->getStatusCode = $statusCode;
        $guzzleResponse->getMockController()->getBody = $return;

        $guzzleRequest = new \mock\Guzzle\Http\Message\Request(uniqid(), uniqid());
        $guzzleRequest->getMockController()->send = $guzzleResponse;

        $client = new \mock\M6Web\Bundle\WSClientBundle\Adapter\Client\GuzzleClientAdapter($guzzleClient);
        $client->getMockController()->createRequest = $guzzleRequest;

        return $client;
    }

    /**
     * @return void
     */
    public function testBasicSetter()
    {
        $this
            ->if($guzzleClient = new \Guzzle\Http\Client())
            ->and($eventDispatcher = new \mock\Symfony\Component\EventDispatcher\EventDispatcher())
            ->and($client = new Base($guzzleClient))
            ->and($client->setBaseUrl($base_url='http://www.m6.fr'))
            ->and($client->setEventDispatcher($eventDispatcher))
            ->and($client->setConfig(array(
                'timeout' => 10,
                'followlocation' => true,
                'maxredirs' => 6
            )))
            ->then
                ->array($guzzleClient->getConfig('curl.options'))
                    ->hasSize(3)
                    ->isEqualTo(array(
                        CURLOPT_TIMEOUT => 10,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_MAXREDIRS => 6
                    ))
                ->string($guzzleClient->getBaseUrl())
                    ->isEqualTo($base_url)
                ->object($guzzleClient->getEventDispatcher())
                    ->isEqualTo($eventDispatcher)

            ->if($client->setConfig([]))
            ->then
                ->array($guzzleClient->getConfig('curl.options'))
                    ->hasSize(1)
                    ->isEqualTo(array(
                        CURLOPT_TIMEOUT => 1
                    ))
        ;
    }

    /**
     * @return void
     */
    public function testGet()
    {
        $this
            ->if($client = $this->buildMockWsClient($status_code=rand(), $body=uniqid()))
            ->and($request = $client->get($url=uniqid()))
            ->and($response = $request->send())
            ->then
                ->variable($response->getStatusCode())
                    ->isIdenticalTo($status_code)
                ->string($response->getBody())
                    ->isIdenticalTo($body);
        ;
    }

    /**
     * @return void
     */
    public function testPost()
    {
        $this
            ->if($client = $this->buildMockWsClient($status_code=rand(), $body=uniqid()))
            ->and($request = $client->post($url='http://www.m6.fr'))
            ->and($response = $request->send())
            ->then
                ->variable($response->getStatusCode())
                    ->isIdenticalTo($status_code)
                ->string($response->getBody())
                    ->isIdenticalTo($body);
    }

    /**
     * @dataProvider httpMethodsProvider
     *
     * @return void
     */
    public function testCreateRequest($method)
    {
        $this
            ->if($client = $this->buildMockWsClient($status_code=rand(), $body=uniqid()))
            ->and($request = $client->createRequest($method, $url=uniqid()))
            ->and($response = $request->send())
            ->then
                ->variable($response->getStatusCode())
                    ->isIdenticalTo($status_code)
                ->string($response->getBody())
                    ->isIdenticalTo($body)
        ;
    }

    /**
     * @return void
     */
    public function testCache()
    {
        $this
            ->if($guzzleClient = new \mock\Guzzle\Http\Client())
            ->and($client = new Base($guzzleClient))
            ->and($cacheService = new \mock\M6Web\Bundle\WSClientBundle\Cache\CacheInterface())

            // without cache service
            ->then
                ->variable($client->shouldResetCache())
                    ->isNull()
                ->exception(function() use ($client, $cacheService) {
                    $client->setCache($ttl=rand(), $cacheService, '\Toto');
                })
                    ->hasMessage('Class "\Toto" doesn\'t exists.')

                ->exception(function() use ($client, $cacheService) {
                    $client->setCache(
                        $ttl=5, 
                        $cacheService, 
                        '\M6Web\Bundle\WSClientBundle\Tests\Units\Adapter\Client\CacheAdpater',  
                        '\Toto'
                    );
                })
                    ->hasMessage('Class "\Toto" doesn\'t exists.')

            // Standard use case
            ->if($client->setCache(
                $ttl=5, 
                $cacheService, 
                $cacheAdapterClass='\M6Web\Bundle\WSClientBundle\Tests\Units\Adapter\Client\CacheAdpater',  
                $cachePluginClass='\M6Web\Bundle\WSClientBundle\Tests\Units\Adapter\Client\CachePlugin'
            ))
            ->then
                ->mock($guzzleClient)
                    ->call('addSubscriber')
                        ->withArguments(new CachePlugin())
                            ->once()
        ;
    }
    
    /**
     * Provide HTTP methods list
     *
     * @return array
     */
    public function httpMethodsProvider()
    {
        return [
            'GET',
            'HEAD',
            'DELETE',
            'PUT',
            'PATCH',
            'POST',
            'OPTIONS'
        ];
    }
}

class CacheAdpater
{
}

class CachePlugin implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [];
    }
}
