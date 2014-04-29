<?php
namespace M6Web\Bundle\WSClientBundle\Tests\Units\Adapter\Client;

require_once __DIR__.'/../../../../../../../../vendor/autoload.php';
require_once 'FakeGuzzleClient.php';

use mageekguy\atoum\test;
use M6Web\Bundle\WSClientBundle\Adapter\Client\GuzzleClientAdapter as BaseGuzzleClientAdapter;
use Guzzle\Http\Client;

/**
* Test
*
* @maxChildrenNumber 1
*/
class GuzzleClientAdapter extends test
{
    /**
     * Construit un mock du client Guzzle
     *
     * @param int    $statusCode Statut de la réponse retrounée
     * @param string $return     Contenu de la réponse retournée
     *
     * @return Guzzle\Http\ClientInterface
     */
    protected function buildMockWsClient($statusCode = 200, $return = 'un retour')
    {
        $wsResponse = new \mock\Guzzle\Http\Message\Response($statusCode);
        $wsResponse->getMockController()->getBody = function() use ($return) {
            return $return;
        };
        $wsResponse->getMockController()->getStatusCode = function() use ($statusCode) {
            return $statusCode;
        };

        $wsClient = new \mock\M6Web\Bundle\WSClientBundle\Tests\Units\Adapter\Client\FakeGuzzleClient();

        $wsClient->getMockController()->createRequest = function($method, $uri) use ($wsResponse) {
                $wsRequest = new \mock\Guzzle\Http\Message\Request($method, $uri);
                $wsRequest->getMockController()->send = function() use ($wsResponse) {
                return $wsResponse;
            };

            return $wsRequest;
        };

        return $wsClient;
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

    /**
     * Teste les setter de base
     *
     * @return void
     */
    public function testBasicSetter()
    {
        $guzzleClient = $this->buildMockWsClient();
        $eventDispatcher = new \mock\Symfony\Component\EventDispatcher\EventDispatcher();

        $client = new BaseGuzzleClientAdapter($guzzleClient);
        $client->setBaseUrl('http://www.m6.fr');
        $client->setEventDispatcher($eventDispatcher);
        $client->setConfig(array(
            'timeout' => 10,
            'followlocation' => true,
            'maxredirs' => 6
        ));

        // Vérification qu'on définit la bonne url de base
        $this
            ->mock($guzzleClient)
                ->call('setBaseUrl')
                    ->withIdenticalArguments('http://www.m6.fr')
                        ->once();

        // Vérification de la définition de l'event dispatcher
        $this
            ->mock($guzzleClient)
                ->call('setEventDispatcher')
                    ->withIdenticalArguments($eventDispatcher)
                        ->once();

        // Vérification qu'on définit la bonne config
        $configWanted = array(
            'curl.options' => array(
                CURLOPT_TIMEOUT => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 6
            )
        );

        $this
            ->mock($guzzleClient)
                ->call('setConfig')
                    ->withIdenticalArguments($configWanted)
                        ->once();

        // Vérification du timeout par défaut
        $client->setConfig(array());
        $configWanted = array(
            'curl.options' => array(
                CURLOPT_TIMEOUT => 1
            )
        );

        $this
            ->mock($guzzleClient)
                ->call('setConfig')
                    ->withIdenticalArguments($configWanted)
                        ->once();
    }

    /**
     * Teste la méthode get
     *
     * @return void
     */
    public function testGet()
    {
        $guzzleClient = $this->buildMockWsClient(200, 1000);

        $client = new BaseGuzzleClientAdapter($guzzleClient);
        $request = $client->get('http://www.google.com');
        $response = $request->send();

        // On vérifie que le corps de la réponse est bien une string
        $this
            ->variable($response->getBody())
                ->isIdenticalTo('1000');

        $this
            ->variable($response->getStatusCode())
                ->isIdenticalTo(200);
    }

    /**
     * Test la méthode post
     *
     * @return void
     */
    public function testPost()
    {
        $guzzleClient = $this->buildMockWsClient(500);

        $client = new BaseGuzzleClientAdapter($guzzleClient);
        $request = $client->post('http://www.m6.fr');
        $response = $request->send();

        $this
            ->variable($response->getBody())
                ->isIdenticalTo('un retour');

        $this
            ->variable($response->getStatusCode())
                ->isIdenticalTo(500);
    }

    /**
     * Test createRequest function
     *
     * @dataProvider httpMethodsProvider
     *
     * @return void
     */
    public function testCreateRequest($method)
    {
        $guzzleClient = $this->buildMockWsClient(500);

        $client = new BaseGuzzleClientAdapter($guzzleClient);
        $request = $client->createRequest($method, 'http://www.m6.fr');
        $response = $request->send();

        $this
            ->variable($response->getBody())
                ->isIdenticalTo('un retour');

        $this
            ->variable($response->getStatusCode())
                ->isIdenticalTo(500);
    }

    /**
     * Test des méthodes relatives au cache
     *
     * @return void
     */
    public function testCache()
    {
        $cacheService = new \mock\M6Web\Bundle\WSClientBundle\Cache\CacheInterface();
        $guzzleClient = $this->buildMockWsClient();

        $client = new BaseGuzzleClientAdapter($guzzleClient);

        // Sans cache resetter
        $this
            ->variable($client->shouldResetCache())
                ->isIdenticalTo(null);

        // On vérifie les exceptions dans le cas de classe inexistante
        $this
            ->exception(function() use ($client, $cacheService) {
                $client->setCache(5, $cacheService, '\Toto');
            });

        $this
            ->exception(function() use ($client, $cacheService) {
                $client->setCache(5, $cacheService, '\M6Web\Bundle\WSClientBundle\Tests\Units\Adapter\Client\CacheAdpater',  '\Toto');
            });

        // On vérifie le fonctionnement normal
        $client->setCache(5, $cacheService, '\M6Web\Bundle\WSClientBundle\Tests\Units\Adapter\Client\CacheAdpater',  '\M6Web\Bundle\WSClientBundle\Tests\Units\Adapter\Client\CachePlugin');

        $this
            ->mock($guzzleClient)
                ->call('addSubscriber')
                    ->withArguments(new CachePlugin())
                        ->once();
    }

    /**
     * testRealsUrls with purge
     * @return void
     */
    public function testRealUrls()
    {
        $guzzleClient = new Client('https://api.github.com');
        $client = new BaseGuzzleClientAdapter($guzzleClient);

        $response = $client->get('http://www.google.fr')->send();
        $this->assert
            ->string($response->getInfo("url"))
            ->isIdenticalTo("http://www.google.fr");

        $response = $client->get('http://www.google.fr?coucou=1')->send();
        $this->assert
            ->string($response->getInfo("url"))
            ->isIdenticalTo("http://www.google.fr?coucou=1");

    }

}

class CacheAdpater
{
}

class CachePlugin implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
    }
}
