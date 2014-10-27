<?php

/*
 * This file is part of the AbimusFetchBundle.
 *
 * (c) Patrik Karisch <patrik.karisch@abimus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Abimus\FetchBundle\Tests;

use Abimus\FetchBundle\ConnectionFactory;
use Abimus\FetchBundle\ConnectionInterface;

/**
 * @author Patrik Karisch <patrik.karisch@abimus.com>
 */
class ConnectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected static $testName = 'default';
    protected static $testParams = array(
        'service' => 'imap',
        'host' => 'mail.example.com',
        'port' => 143,
        'username' => 'foo',
        'password' => 'bar',
        'encryption' => 'tls',
        'validate_cert' => true,
    );

    public function testCreate()
    {
        $factory = new ConnectionFactory();
        $connection = $factory->createConnection(self::$testName, self::$testParams);
        $this->assertTrue($connection instanceof ConnectionInterface);
    }
}
