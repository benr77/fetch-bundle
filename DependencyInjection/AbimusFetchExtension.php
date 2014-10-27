<?php

/*
 * This file is part of the AbimusFetchBundle.
 *
 * (c) Patrik Karisch <patrik.karisch@abimus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Abimus\FetchBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Enabling the option to obtain multiple connections via service
 * container is inspired by the DoctrineBundle.
 *
 * @author Patrik Karisch <patrik.karisch@abimus.com>
 */
class AbimusFetchExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('fetch.xml');

        if (empty($config['default_connection'])) {
            $keys = array_keys($config['connections']);
            $config['default_connection'] = reset($keys);
        }
        $this->defaultConnection = $config['default_connection'];

        $connections = array();
        foreach (array_keys($config['connections']) as $name) {
            $connections[$name] = sprintf('abimus_fetch.%s_connection', $name);
        }
        $container->setParameter('abimus_fetch.connections', $connections);
        $container->setParameter('abimus_fetch.default_connection', $this->defaultConnection);

        foreach ($config['connections'] as $name => $params) {
            $this->loadConnection($name, $params, $container);
        }

        $container->setParameter('abimus_fetch.connection_params', $config);
    }

    /**
     * Loads a configured connection.
     *
     * @param string           $name      The name of the connection
     * @param array            $params    A connection configuration.
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    protected function loadConnection($name, array $params, ContainerBuilder $container)
    {
        $container
            ->setDefinition(sprintf('abimus_fetch.%s_connection', $name), new DefinitionDecorator('abimus_fetch.connection'))
            ->setArguments(array(
                $name,
                $params,
            )
        );
    }
}
