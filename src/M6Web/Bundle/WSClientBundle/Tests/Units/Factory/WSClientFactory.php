<?php
namespace M6Web\Bundle\WSClientBundle\Tests\Units\Factory;

require_once __DIR__.'/../../../../../../../vendor/autoload.php';

use mageekguy\atoum\test;

use M6Web\Bundle\WSClientBundle\Factory\WSClientFactory as Base;

/**
* Test WSClientFactory
*
* @maxChildrenNumber 1
*/
class WSClientFactory extends test
{

    /**
     * Instanciate tested class
     *
     * @return WSClientFactory
     */
    public function getFactoryInstance()
    {
        return new Base;
    }

    /**
     * Instanciate fake container
     *
     * @return Container
     */
    public function getContainerInstance()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();

        $container               = new \mock\Symfony\Component\DependencyInjection\Container;
        $containerMockController = $container->getMockController();
        $self                    = $this;

        $currentScope = null;

        $containerMockController->isScopeActive = function($scope) use(&$currentScope) {
            return $scope = $currentScope;
        };

        $containerMockController->enterScope = function($scope) use(&$currentScope) {
            return $currentScope = $scope;
        };

        $containerMockController->get = function($id) use($self) {
            $self->mockGenerator->orphanize('__construct');
            $self->mockGenerator->shuntParentClassCalls();

            switch ($id) {
                case 'request':
                    $request = new \mock\Symfony\Component\HttpFoundation\Request;
                    $request->getMockController()->getHttpHost = 'localhost';
                    return $request;

                case 'event_dispatcher':
                    $eventDispatcher = new \mock\Symfony\Component\EventDispatcher\EventDispatcher;
                    return $eventDispatcher;

                default:

                    return null;
            }

        };

        return $container;
    }

    /**
     * Test getClient function
     *
     * @return void
     */
    public function testGetClient()
    {
        $factory   = $this->getFactoryInstance();
        $container = $this->getContainerInstance();

        $this
            ->exception(function() use($factory, $container) {
                $factory->getClient($container, '', [], 'Foo/Bar/Class');
            })
                ->isInstanceOf('\InvalidArgumentException')
        ;

        $client = $factory->getClient($container, 'http://www.foobar.com');

        $this
            ->string($client->getBaseUrl())
                ->isEqualTo('http://www.foobar.com')
        ;

        $container->enterScope('request');

        $this
            ->object($client = $factory->getClient($container))
                ->isInstanceOf('M6Web\Bundle\WSClientBundle\Adapter\Client\GuzzleClientAdapter')
            ->string($client->getBaseUrl())
                ->isEqualTo('http://localhost')
        ;
    }

}