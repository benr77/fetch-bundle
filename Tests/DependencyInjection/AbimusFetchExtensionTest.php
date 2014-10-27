<?php

/*
 * This file is part of the AbimusFetchBundle.
 *
 * (c) Patrik Karisch <patrik.karisch@abimus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Abimus\FetchBundle\Tests\DependencyInjection;

use Abimus\FetchBundle\DependencyInjection\AbimusFetchExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Patrik Karisch <patrik.karisch@abimus.com>
 */
class AbimusFetchExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Abimus\FetchBundle\DependencyInjection\AbimusFetchExtension
     */
    private $extension;

    /**
     * Root name of the configuration
     *
     * @var string
     */
    private $root;

    public function setUp()
    {
        parent::setUp();

        $this->extension = $this->getExtension();
        $this->root      = 'abimus_fetch';
    }

    public function testConfigWithDefaultValues()
    {
        $required = array(
            'host' => 'mail.example.com',
            'default_connection' => 'default',
        );

        $this->extension->load(array($required), $container = $this->getContainer());

        $expected = array(
            'default_connection' => 'default',
            'connections' => array(
                'default' => array(
                    'service' => 'imap',
                    'host' => 'mail.example.com',
                    'port' => 143,
                    'encryption' => 'tls',
                    'validate_cert' => true,
                ),
            ),
        );

        $this->assertTrue($container->hasParameter($this->root . '.connection_params'));
        $this->assertEquals($expected, $container->getParameter($this->root . '.connection_params'));
    }

    public function testConfigWithOverrideValues()
    {
        $configs = array(
            'connection' => array(
                'name' => 'not_default',
                'service' => 'pop3',
                'host' => 'mail.example.com',
                'port' => 110,
                'username' => 'foo',
                'password' => 'bar',
                'mailbox' => 'INBOX',
                'encryption' => false,
                'validate_cert' => false,
            ),
        );

        $this->extension->load(array($configs), $container = $this->getContainer());

        $expected =  array(
            'default_connection' => 'not_default',
            'connections' => array(
                'not_default' => array(
                    'service' => 'pop3',
                    'host' => 'mail.example.com',
                    'port' => 110,
                    'username' => 'foo',
                    'password' => 'bar',
                    'mailbox' => 'INBOX',
                    'encryption' => false,
                    'validate_cert' => false,
                ),
            ),
        );

        $this->assertTrue($container->hasParameter($this->root . '.connection_params'));
        $this->assertEquals($expected, $container->getParameter($this->root . '.connection_params'));
    }

    /**
     * @dataProvider invalidDataProvider
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testConfigWithInvalidValues($configs)
    {
        $this->extension->load(array($configs), $container = $this->getContainer());
    }

    public function invalidDataProvider()
    {
        return array(
            array(array()),
            array(array('host' => '')),
            array(array('service' => 'foo', 'host' => 'mail.example.com')),
            array(array('service' => '', 'host' => 'mail.example.com')),
            array(array('host' => 'mail.example.com', 'port' => 123456789)),
            array(array('host' => 'mail.example.com', 'port' => 'foo')),
            array(array('host' => 'mail.example.com', 'port' => '')),
            array(array('host' => 'mail.example.com', 'username' => null)),
            array(array('host' => 'mail.example.com', 'username' => '')),
            array(array('host' => 'mail.example.com', 'password' => null)),
            array(array('host' => 'mail.example.com', 'password' => '')),
            array(array('host' => 'mail.example.com', 'mailbox' => null)),
            array(array('host' => 'mail.example.com', 'mailbox' => '')),
            array(array('host' => 'mail.example.com', 'encryption' => 'foo')),
            array(array('host' => 'mail.example.com', 'encryption' => '')),
            array(array('host' => 'mail.example.com', 'validate_cert' => 'foo')),
            array(array('host' => 'mail.example.com', 'validate_cert' => '')),
        );
    }

    /**
     * Returns the Configuration to test
     *
     * @return \Abimus\FetchBundle\DependencyInjection\AbimusFetchExtension
     */
    protected function getExtension()
    {
        return new AbimusFetchExtension();
    }

    /**
     * @return Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private function getContainer()
    {
        $container = new ContainerBuilder();

        return $container;
    }
}
