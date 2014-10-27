<?php

/*
 * This file is part of the AbimusFetchBundle.
 *
 * (c) Patrik Karisch <patrik.karisch@abimus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Abimus\FetchBundle\Fetch;

use Fetch\Server as BaseServer;

/**
 * Extending \Fetch\Server with some extra functionallity
 *
 * @author Patrik Karisch <patrik.karisch@abimus.com>
 */
class Server extends BaseServer
{
    public function __construct() {}

    /**
     * Takes the location and service thats trying to be connected to as its arguments.
     *
     * @param string      $serverPath
     * @param null|int    $port
     * @param null|string $service
     */
    public function init($serverPath, $port = 143, $service = 'imap')
    {
        parent::__construct($serverPath, $port, $service);
    }

    /**
     * @codeCoverageIgnore
     */
    public function setImapStream()
    {
        parent::setImapStream();
    }
}
