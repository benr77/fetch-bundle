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
 * Defines a contract which functionality every connection must implement.
 *
 * @author Patrik Karisch <patrik.karisch@abimus.com>
 */
interface ConnectionInterface extends \Countable
{
    /**
     * Get the configured name of the connection.
     *
     * @return string The connection name
     */
    public function getName();

    /**
     * Get the service type
     *
     * @return string imap, pop3 or nntp
     */
    public function getService();

    /**
     * Get the configured host to connect
     *
     * @return string The hostname
     */
    public function getHost();

    /**
     * Get the configured port to connect
     *
     * @return int The port
     */
    public function getPort();

    /**
     * Get the configured username to connect
     *
     * @return string The username
     */
    public function getUsername();

    /**
     * Get the configured mailbox to connect
     *
     * @return string The username
     */
    public function getMailbox();

    /**
     * Get the configured encryption
     *
     * @return mixed tls, ssl or false
     */
    public function getEncryption();

    /**
     * Is the certificate validated or not
     *
     * @return bool true if validated
     */
    public function isValidateCert();

    /**
     * This function returns an array of ImapMessage object for emails that fit the criteria passed. The criteria string
     * should be formatted according to the imap search standard, which can be found on the php "imap_search" page or in
     * section 6.4.4 of RFC 2060
     *
     * @link http://us.php.net/imap_search
     * @link http://www.faqs.org/rfcs/rfc2060
     * @param  string   $criteria
     * @param  null|int $limit
     * @return array    An array of ImapMessage objects
     */
    public function search($criteria = 'ALL', $limit = null);

    /**
     * This function returns the recently received emails as an array of ImapMessage objects.
     *
     * @param  null|int $limit
     * @return array    An array of ImapMessage objects for emails that were recently received by the server.
     */
    public function getRecentMessages($limit = null);

    /**
     * Returns the emails in the current mailbox as an array of ImapMessage objects.
     *
     * @param  null|int  $limit
     * @return Message[]
     */
    public function getMessages($limit = null);

    /**
     * This function removes all of the messages flagged for deletion from the mailbox.
     *
     * @return bool
     */
    public function expunge();

    /**
     * Checks if the given mailbox exists.
     *
     * @param $mailbox
     *
     * @return bool
     */
    public function hasMailbox($mailbox);

    /**
     * Creates the given mailbox.
     *
     * @param $mailbox
     *
     * @return bool
     */
    public function createMailbox($mailbox);

    /**
     * Reconnect to the server
     */
    public function reconnect();
}
