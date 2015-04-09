<?php
namespace M6Web\Bundle\WSClientBundle\DependencyInjection\tests\units;

use mageekguy\atoum;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use M6Web\Bundle\WSClientBundle\DependencyInjection\M6WebWSClientExtension as BaseM6WebWSClientExtension;
use Symfony\Component\EventDispatcher\EventDispatcher;

class M6WebWSClientExtension extends atoum\test
{

    /**
     * @var BaseM6WebWSClientExtension
     */
    protected $extension;

    /**
     * @var ContainerBuilder
     */
    protected $container;


    protected function initContainer()
    {
        $this->extension = new BaseM6WebWSClientExtension();
        $this->container = new ContainerBuilder();
        $this->container->register('event_dispatcher', new EventDispatcher());
        $this->container->registerExtension($this->extension);
        $this->container->setParameter('kernel.debug', true);
    }


    /**
     * @param ContainerBuilder $container
     * @param $resource
     */
    protected function loadConfiguration(ContainerBuilder $container, $resource)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../Fixtures/'));
        $loader->load($resource.'.yml');
    }

    public function testBasicConfiguration()
    {
        $this->initContainer();
        $this->loadConfiguration($this->container, 'basic_config');
        $this->container->compile();

        $this->assert
            ->boolean($this->container->has('m6_ws_client'))
                ->isIdenticalTo(true)
            ->and()
            ->object($serviceWS = $this->container->get('m6_ws_client'))
                ->isInstanceOf('M6Web\Bundle\WSClientBundle\Adapter\Client\GuzzleClientAdapter')
        ;

        $this->assert
            ->object($serviceWS->get('http://www.google.com'))
                ->isInstanceOf('M6Web\Bundle\WSClientBundle\Adapter\Response\GuzzleResponseAdapter')
        ;
    }

    public function testFutureConfiguration(){
        $this->initContainer();
        $this->loadConfiguration($this->container, 'future_config');
        $this->container->compile();

        $this->if($serviceWS = $this->container->get('m6_ws_client'))
            ->object($serviceWS->get('http://www.google.com'))
            ->isInstanceOf('M6Web\Bundle\WSClientBundle\Adapter\Response\GuzzleResponseAdapter')
        ;
    }

}