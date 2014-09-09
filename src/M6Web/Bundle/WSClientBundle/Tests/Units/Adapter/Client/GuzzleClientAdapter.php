<?php
namespace M6Web\Bundle\WSClientBundle\Tests\Units\Adapter\Client;

use mageekguy\atoum\test;
use M6Web\Bundle\WSClientBundle\Adapter\Client\GuzzleClientAdapter as Base;
use M6Web\Bundle\WSClientBundle\Adapter\Request\GuzzleRequestAdapter;
use M6Web\Bundle\WSClientBundle\Adapter\Response\GuzzleResponseAdapter;
use Guzzle\Http\Client;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;

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
    protected function buildMockWsClient($statusCode=200, $return=null, $headers = [])
    {
        $guzzleClient = new \mock\GuzzleHttp\Client();

        $guzzleRequest = new \mock\GuzzleHttp\Message\Request(uniqid(), uniqid());

        $guzzleResponse = new \mock\GuzzleHttp\Message\Response($statusCode, $headers);
        $guzzleResponse->getMockController()->getStatusCode = $statusCode;
        $guzzleResponse->getMockController()->getBody       = $return;

        $client = new \mock\M6Web\Bundle\WSClientBundle\Adapter\Client\GuzzleClientAdapter($guzzleClient);
        $client->getMockController()->createRequest = new GuzzleRequestAdapter($guzzleRequest);
        $client->getMockController()->send = new GuzzleResponseAdapter($guzzleResponse);
        $client->getMockController()->post = new GuzzleResponseAdapter($guzzleResponse);
        $client->getMockController()->get = new GuzzleResponseAdapter($guzzleResponse);

        return $client;
    }

    /**
     * @return void
     */
    public function testBasicSetter()
    {
        $this
            ->if($guzzleClient = new \GuzzleHttp\Client())
            ->and($eventDispatcher = new \mock\Symfony\Component\EventDispatcher\EventDispatcher())
            ->and($client = new Base($guzzleClient))
            ->then
                ->object($guzzleClient->getEmitter())
                    ->isInstanceOf('\GuzzleHttp\Event\Emitter')
        ;
    }

    /**
     * @return void
     */
    public function testGet()
    {
        $this
            ->if($client = $this->buildMockWsClient($status_code=rand(), $body=uniqid()))
            ->and($response = $client->get($url=uniqid()))
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
            ->and($response = $client->post($url='http://www.m6.fr'))
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
            ->and($response = $client->send($request))
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
            ->if($guzzleClient = new \mock\GuzzleHttp\Client())
            ->and($client = new Base($guzzleClient))
            ->and($cacheService = new \mock\M6Web\Bundle\WSClientBundle\Cache\CacheInterface())

            // without cache service
            ->then
                ->variable($client->shouldResetCache())
                    ->isNull()
                ->exception(function() use ($client, $cacheService) {
                    $client->setCache($ttl=rand(), false, $cacheService, '\Toto');
                })
                    ->hasMessage('Class "\Toto" doesn\'t exists or doesn\'t implement Doctrine\Common\Cache\Cache.')

                ->exception(function() use ($client, $cacheService) {
                    $client->setCache(
                        $ttl=5, false,
                        $cacheService,
                        '\M6Web\Bundle\WSClientBundle\Tests\Units\Adapter\Client\CacheAdpater',
                        '\Toto'
                    );
                })
                    ->hasMessage('Class "\Toto" doesn\'t exists or doesn\'t implement GuzzleHttp\Subscriber\Cache\CacheStorageInterface.')

            // Standard use case
            ->if($client->setCache(
                $ttl=5, false,
                $cacheService,
                $cacheAdapterClass='\M6Web\Bundle\WSClientBundle\Tests\Units\Adapter\Client\CacheAdpater',
                $cacheStorageClass='\M6Web\Bundle\WSClientBundle\Tests\Units\Adapter\Client\CacheStorage'
            ))
            ->then
                ->mock($guzzleClient)
                    ->call('getEmitter')
                        ->once()
        ;
    }

    /**
     * testHeader
     */
    public function testHeaders()
    {
        $headers = [
            'X-Rest-Collection-Count'         => 250,
            'X-Rest-Collection-Limit'         => 20,
            'X-Rest-Collection-Count-Content' => 20
        ];

        $this
            ->if($client = $this->buildMockWsClient($statusCode = rand(), $body = uniqid(), $headers))
            ->and($response = $client->get($url=uniqid()))
            ->then
                ->variable($response->getStatusCode())
                    ->isIdenticalTo($statusCode)
                ->string($response->getBody())
                    ->isIdenticalTo($body)
                ->array($response->getHeaders())
                    ->hasKeys(array_keys($headers))
                ->string($response->getHeader('X-Rest-Collection-Count'))
                    ->isEqualTo($headers['X-Rest-Collection-Count'])
                ->string($response->getHeader('X-Rest-Collection-Limit'))
                    ->isEqualTo($headers['X-Rest-Collection-Limit']);
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

class CacheAdpater implements \Doctrine\Common\Cache\Cache
{
    function fetch($id)
    {

    }

    function contains($id)
    {

    }

    function save($id, $data, $lifeTime = 0)
    {

    }

    function delete($id)
    {

    }

    function getStats()
    {

    }
}

class CacheStorage implements \GuzzleHttp\Subscriber\Cache\CacheStorageInterface
{
    public function fetch(RequestInterface $request)
    {

    }

    public function cache(
        RequestInterface $request,
        ResponseInterface $response
    )
    {

    }

    public function delete(RequestInterface $request)
    {

    }

    public function purge($url)
    {

    }
}
