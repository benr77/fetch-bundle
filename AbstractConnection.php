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

/**
 * Base connection class, which provides generall functionality.
 *
 * @author Patrik Karisch <patrik.karisch@abimus.com>
 */
abstract class AbstractConnection implements ConnectionInterface
{
    /**#@+
     * @var string
     */
    protected $name;
    protected $service;
    protected $host;
    protected $username;
    protected $password;
    protected $mailbox;
    /**#@-*/

    /**
     * @var int
     */
    protected $port;

    /**
     * @var string|bool
     */
    protected $encryption;

    /**
     * @var bool
     */
    protected $validateCert;

    /**
     * Init a new connection
     *
     * @param string $name   The connection name
     * @param array  $params Array with connection parameters
     */
    public function __construct($name, array $params)
    {
        $keys = array('service', 'host', 'port');
        foreach ($keys as $key) {
            if (!array_key_exists($key, $params)) {
                throw new \InvalidArgumentException(sprintf('Missing param "%s"', $key));
            }
        }

        $this->name = $name;
        $this->service = $params['service'];
        $this->host = $params['host'];
        $this->port = $params['port'];
        $this->username = !empty($params['username']) ? $params['username'] : null;
        $this->password = !empty($params['password']) ? $params['password'] : null;
        $this->mailbox = !empty($params['mailbox']) ? $params['mailbox'] : null;
        $this->encryption = !empty($params['encryption']) ? $params['encryption'] : null;;
        $this->validateCert = !empty($params['validate_cert']) ? true : false;

        $this->create();
    }

    /**
     * Create the connection
     */
    abstract protected function create();

    public function getName()
    {
        return $this->name;
    }

    public function getService()
    {
        return $this->service;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getMailbox()
    {
        return $this->mailbox;
    }

    public function getEncryption()
    {
        return $this->encryption;
    }

    public function isValidateCert()
    {
        return $this->validateCert;
    }
}
