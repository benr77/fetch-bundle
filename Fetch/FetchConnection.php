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
use Abimus\FetchBundle\AbstractConnection as BaseConnection;
use Abimus\FetchBundle\ConnectionInterface;

/**
 * A Fetch connection wrapping \Fetch\Server
 *
 * @author Patrik Karisch <patrik.karisch@abimus.com>
 */
final class FetchConnection extends BaseConnection implements ConnectionInterface
{
    /**
     * @var Server
     */
    protected $server;

    public function __construct($name, $params, Server $server = null)
    {
        $this->server = $server ?: new Server();
        parent::__construct($name, $params);
    }

    protected function create()
    {
        $this->server->init($this->host, $this->port, $this->service);

        if ($this->username && $this->password) {
            $this->server->setAuthentication($this->username, $this->password);
        }

        $this->server->setFlag('tls', false);
        $this->server->setFlag('ssl', false);
        if ($this->encryption) {
            $this->server->setFlag($this->encryption);
        }

        if ($this->validateCert) {
            $this->server->setFlag('validate-cert');
        } else {
            $this->server->setFlag('novalidate-cert');
        }

        if ($this->mailbox) {
            $this->server->setMailBox($this->mailbox);
        }
    }

    public function search($criteria = 'ALL', $limit = null)
    {
        return $this->server->search($criteria, $limit);
    }

    public function getRecentMessages($limit = null)
    {
        return $this->server->getRecentMessages($limit);
    }

    public function getMessages($limit = null)
    {
        return $this->server->getMessages($limit);
    }
    
    public function getOrderedMessages($orderBy, $reverse, $limit)
    {
        return $this->server->getOrderedMessages($orderBy, $reverse, $limit);
    }

    public function expunge()
    {
        return $this->server->expunge();
    }

    public function hasMailbox($mailbox)
    {
        return $this->server->hasMailBox($mailbox);
    }

	public function listMailBox($pattern = '*')
    {
        return $this->server->listMailboxes($pattern);
    }

    public function createMailbox($mailbox)
    {
        return $this->server->createMailBox($mailbox);
    }

    public function getMailBoxDetails($mailbox)
    {
        return $this->server->getMailBoxDetails($mailbox);
    }

    public function count()
    {
        return $this->server->numMessages();
    }

    public function reconnect()
    {
        $this->server->setImapStream();
    }
}
