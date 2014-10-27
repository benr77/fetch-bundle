<?php

/*
 * This file is part of the AbimusFetchBundle.
 *
 * (c) Patrik Karisch <patrik.karisch@abimus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Abimus\FetchBundle\Tests\Fetch;

use Abimus\FetchBundle\Fetch\FetchConnection;

/**
 * @author Patrik Karisch <patrik.karisch@abimus.com>
 */
class FetchConnectionTest extends \PHPUnit_Framework_TestCase
{
    protected static $testName = 'default';
    protected static $testParams = array(
        'service' => 'imap',
        'host' => 'mail.example.com',
        'port' => 143,
        'username' => 'foo',
        'password' => 'bar',
        'mailbox' => 'INBOX',
        'encryption' => 'tls',
        'validate_cert' => true,
    );

    public function testConstructur()
    {
        $connection = new FetchConnection(self::$testName, self::$testParams, $this->getMockServer());

        $this->assertEquals(self::$testName, $connection->getName());
        $this->assertEquals(self::$testParams['service'], $connection->getService());
        $this->assertEquals(self::$testParams['host'], $connection->getHost());
        $this->assertEquals(self::$testParams['port'], $connection->getPort());
        $this->assertEquals(self::$testParams['username'], $connection->getUsername());
        $this->assertEquals(self::$testParams['mailbox'], $connection->getMailbox());
        $this->assertEquals(self::$testParams['encryption'], $connection->getEncryption());
        $this->assertEquals(self::$testParams['validate_cert'], $connection->isValidateCert());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testEmptyParams()
    {
        new FetchConnection(self::$testName, array(), $this->getMockServer());
    }

    public function testCreate()
    {
        $server = $this->getMockServer();
        $server->expects($this->once())
               ->method('init')
               ->with($this->equalTo(self::$testParams['host']), $this->equalTo(self::$testParams['port']), $this->equalTo(self::$testParams['service']));
        $server->expects($this->once())
               ->method('setAuthentication')
               ->with($this->equalTo(self::$testParams['username']), $this->equalTo(self::$testParams['password']));
        $server->expects($this->exactly(4))
               ->method('setFlag')
               ->with($this->logicalOr('tls', 'ssl', 'validate-cert'), $this->logicalOr(true, null));
        $server->expects($this->once())
               ->method('setMailbox')
               ->with(self::$testParams['mailbox']);

        new FetchConnection(self::$testName, self::$testParams, $server);
    }

    public function testNoValidateCert()
    {
        $server = $this->getMockServer();
        $server->expects($this->exactly(4))
               ->method('setFlag')
               ->with($this->logicalOr('tls', 'ssl', 'novalidate-cert'), $this->logicalOr(true, null));

        $params = self::$testParams;
        $params['validate_cert'] = false;
        new FetchConnection(self::$testName, $params, $server);
    }

    public function testSearch()
    {
        $server = $this->getMockServer();
        $server->expects($this->once())
               ->method('search')
               ->with('ALL', 10)
               ->will($this->returnValue(array()));

        $connection = new FetchConnection(self::$testName, self::$testParams, $server);
        $this->assertTrue(is_array($connection->search('ALL', 10)));
    }

    public function testGetRecentMessages()
    {
        $server = $this->getMockServer();
        $server->expects($this->once())
               ->method('getRecentMessages')
               ->with(10)
               ->will($this->returnValue(array()));

        $connection = new FetchConnection(self::$testName, self::$testParams, $server);
        $this->assertTrue(is_array($connection->getRecentMessages(10)));
    }

    public function testGetMessages()
    {
        $server = $this->getMockServer();
        $server->expects($this->once())
               ->method('getMessages')
               ->with(10)
               ->will($this->returnValue(array()));

        $connection = new FetchConnection(self::$testName, self::$testParams, $server);
        $this->assertTrue(is_array($connection->getMessages(10)));
    }

    public function testHasMailbox()
    {
        $server = $this->getMockServer();
        $server->expects($this->once())
               ->method('hasMailBox')
               ->with('foobar')
               ->will($this->returnValue(true));

        $connection = new FetchConnection(self::$testName, self::$testParams, $server);
        $this->assertTrue($connection->hasMailbox('foobar'));
    }

    public function testCreateMailbox()
    {
        $server = $this->getMockServer();
        $server->expects($this->once())
               ->method('createMailBox')
               ->with('foobar')
               ->will($this->returnValue(true));

        $connection = new FetchConnection(self::$testName, self::$testParams, $server);
        $this->assertTrue($connection->createMailbox('foobar'));
    }

    public function testExpunge()
    {
        $server = $this->getMockServer();
        $server->expects($this->once())
               ->method('expunge');

        $connection = new FetchConnection(self::$testName, self::$testParams, $server);
        $connection->expunge();
    }

    public function testReconnect()
    {
        $server = $this->getMockServer();
        $server->expects($this->once())
               ->method('setImapStream');

        $connection = new FetchConnection(self::$testName, self::$testParams, $server);
        $connection->reconnect();
    }

    public function testCountable()
    {
        $server = $this->getMockServer();
        $server->expects($this->once())
               ->method('numMessages')
               ->will($this->returnValue(5));

        $connection = new FetchConnection(self::$testName, self::$testParams, $server);
        $this->assertEquals(5, count($connection));
    }

    protected function getMockServer()
    {
        $server = $this->getMockBuilder('Abimus\FetchBundle\Fetch\Server')
                           ->disableOriginalConstructor()
                           ->getMock();

        return $server;
    }
}
