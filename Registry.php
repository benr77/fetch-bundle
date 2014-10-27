<?php

/*
 * This file is part of the AbimusFetchBundle.
 *
 * (c) Patrik Karisch <patrik.karisch@abimus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Abimus\FetchBundle;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The registry is inspired by the DoctrineBundle.
 *
 * @author Patrik Karisch <patrik.karisch@abimus.com>
 */
class Registry implements ContainerAwareInterface
{
    /**
     * @var array
     */
    protected $connections;

    /**
     * @var string
     */
    protected $defaultConnection;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Construct.
     *
     * @param ContainerInterface $container
     * @param array              $connections
     * @param string             $defaultConnection
     */
    public function __construct(ContainerInterface $container, array $connections, $defaultConnection)
    {
        $this->setContainer($container);
        $this->connections = $connections;
        $this->defaultConnection = $defaultConnection;
    }

    /**
     * Gets the default connection name.
     *
     * @return string The default connection name.
     */
    public function getDefaultConnectionName()
    {
        return $this->defaultConnection;
    }

    /**
     * Gets the named connection.
     *
     * @param string $name The connection name (null for the default one).
     *
     * @return \Fetch\Server The instance of the given connection.
     */
    public function getConnection($name = null)
    {
        if (null === $name) {
            $name = $this->defaultConnection;
        }

        if (!isset($this->connections[$name])) {
            throw new \InvalidArgumentException(sprintf('Fetch Connection named "%s" does not exist.', $name));
        }

        return $this->getService($this->connections[$name]);
    }

    /**
     * Gets an array of all registered connections.
     *
     * @return array An array of Connection instances.
     */
    public function getConnections()
    {
        $connections = array();
        foreach ($this->connections as $name => $id) {
            $connections[$name] = $this->getService($id);
        }

        return $connections;
    }

    /**
     * Gets all connection names.
     *
     * @return array An array of connection names.
     */
    public function getConnectionNames()
    {
        return $this->connections;
    }

    /**
     * Fetches/creates the given services.
     *
     * A service in this context is a connection instance.
     *
     * @param string $name The name of the service.
     *
     * @return \Fetch\Server The instance of the given service.
     */
    protected function getService($name)
    {
        return $this->container->get($name);
    }
}
