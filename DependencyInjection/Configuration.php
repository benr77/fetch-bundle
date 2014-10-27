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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Enabling the option to define multiple connection is inspired
 * by the DoctrineBundle.
 *
 * @author Patrik Karisch <patrik.karisch@abimus.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('abimus_fetch');

        $rootNode
            ->beforeNormalization()
                ->ifTrue(function ($v) { return is_array($v) && !array_key_exists('connections', $v) && !array_key_exists('connection', $v); })
                ->then(function ($v) {
                    // Key that should not be rewritten to the connection config
                    $excludedKeys = array('default_connection' => true);
                    $connection = array();
                    foreach ($v as $key => $value) {
                        if (isset($excludedKeys[$key])) {
                            continue;
                        }
                        $connection[$key] = $v[$key];
                        unset($v[$key]);
                    }
                    $v['default_connection'] = isset($v['default_connection']) ? (string) $v['default_connection'] : 'default';
                    $v['connections'] = array($v['default_connection'] => $connection);

                    return $v;
                })
            ->end()
            ->children()
                ->scalarNode('default_connection')->end()
            ->end()
            ->fixXmlConfig('connection')
            ->append($this->getConnectionsNode());

        return $treeBuilder;
    }

    /**
     * Return the connections node
     *
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    private function getConnectionsNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('connections');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->enumNode('service')
                        ->defaultValue('imap')
                        ->treatNullLike('imap')
                        ->values(array('imap', 'pop3', 'nntp'))
                        ->info('service used for the connection, either imap, pop3 or nntp')
                    ->end()
                    ->scalarNode('host')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->integerNode('port')
                        ->defaultValue(143)
                        ->treatNullLike(143)
                        ->min(1)->max(65535)
                    ->end()
                    ->scalarNode('username')
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('password')
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('mailbox')
                        ->cannotBeEmpty()
                    ->end()
                    ->enumNode('encryption')
                        ->defaultValue('tls')
                        ->treatNullLike('tls')
                        ->values(array('tls', 'ssl', false))
                        ->info('either use encryption or not, possible values are tls, ssl or false')
                    ->end()
                    ->booleanNode('validate_cert')
                        ->defaultTrue()
                        ->treatNullLike(true)
                        ->info('validate server certificate or not')
                    ->end()
                ->end()
            ->end()
        ->end();

        return $node;
    }
}
