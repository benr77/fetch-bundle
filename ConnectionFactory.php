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

use Abimus\FetchBundle\Fetch\FetchConnection;

/**
 * Builds a connection with the Fetch\Server class and configuration
 * parameters from config.
 *
 * @author Patrik Karisch <patrik.karisch@abimus.com>
 */
class ConnectionFactory
{
    /**
     * Creates the connection.
     *
     * @return ConnectionInterface
     */
    public function createConnection($name, array $params)
    {
        $connection = new FetchConnection($name, $params);

        return $connection;
    }
}
