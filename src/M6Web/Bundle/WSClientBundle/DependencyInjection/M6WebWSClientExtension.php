<?php

namespace M6Web\Bundle\WSClientBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class M6WebWSClientExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config['clients'] as $alias => $configClient) {
            $this->loadClient($container, $alias, $configClient);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (!$config['disable_data_collector']) {
            $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
            $loader->load('data_collector.yml');
        }
    }

    /**
     * Load a client configuration as a service in the container
     * @param ContainerInterface $container The container
     * @param string             $alias     Alias of the client
     * @param array              $config    Base config of the client
     *
     * @return void
     */
    protected function loadClient(ContainerInterface $container, $alias, array $config)
    {
        $serviceId  = 'm6_ws_client' . ($alias != 'default' ? '_' . $alias : '');
        $definition = new Definition('M6Web\Bundle\WSClientBundle\Adapter\WSClientAdapterInterface');
        $definition->setFactoryService('m6_ws_client.factory');
        $definition->setFactoryMethod('getClient');
        $definition->setScope(ContainerInterface::SCOPE_CONTAINER);

        $definition->addArgument(new Reference('service_container'));
        $definition->addArgument(array_key_exists('base_url', $config) ? $config['base_url'] : '');
        $definition->addArgument(array_key_exists('config', $config) ? $config['config'] : array());
        $definition->addArgument(array_key_exists('adapter_class', $config) ? $config['adapter_class'] : '');

        $definition->addMethodCall('setEventDispatcher', array(new Reference('event_dispatcher')));
        $definition->addMethodCall('setStopWatch', array(new Reference('debug.stopwatch', ContainerInterface::NULL_ON_INVALID_REFERENCE)));

        if (array_key_exists('cache', $config)) {

            // Service, Adapter & storage
            $cacheService = array_key_exists('service', $config['cache']) ? new Reference($config['cache']['service']) : null;
            $adapterClass = array_key_exists('adapter', $config['cache']) ? $config['cache']['adapter'] : '';
            $storageClass = array_key_exists('storage', $config['cache']) ? $config['cache']['storage'] : '';

            // Subscriber
            $subscriberClass = array_key_exists('subscriber', $config['cache']) ? $config['cache']['subscriber'] : '';

            // Force ttl : ForcedCacheRequest can cache class
            if ($config['cache']['force_request_ttl']) {
                $definition->addMethodCall('setRequestTtl', array($config['cache']['ttl']));
                $canCacheClass = ['\M6Web\Bundle\WSClientBundle\Cache\Guzzle\ForcedCacheRequest', 'canCacheRequest'];
            } else {
                // Config or default value
                $canCacheClass = array_key_exists('can_cache', $config['cache']) ? $config['cache']['can_cache'] : null;
            }

            // Add call to the client setCache
            $definition->addMethodCall('setCache', array(
                $config['cache']['ttl'],
                $config['cache']['force_request_ttl'],
                [
                    'cache_service' => $cacheService,
                    'adapter_class' => $adapterClass,
                    'storage_class' => $storageClass,
                    'subscriber_class' => $subscriberClass,
                    'can_cache_callable' => $canCacheClass
                ],
                array_key_exists('options', $config['cache']) ? $config['cache']['options'] : []
            ));
        }

        $container->setDefinition($serviceId, $definition);
    }

    /**
     * select an alias for the extension
     *
     * trick allowing bypassing the Bundle::getContainerExtension check on getAlias
     * not very clean, to investigate
     *
     * @return string
     */
    public function getAlias()
    {
        return 'm6_ws_client';
    }
}
