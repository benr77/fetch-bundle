Getting Started With AbimusFetchBundle
======================================

Installation
------------

The most easy way to install the library is via composer. To do so, you have to do
the following:

``` bash
$ php composer.phar require abimus/fetch-bundle
```

Now enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Abimus\FetchBundle\AbimusFetchBundle(),
    );
}
```

Configuration
-------------

You can configure the server connection in the `config.yml`

``` yaml
abimus_fetch:

    # service used for the connection, either imap, pop3 or nntp
    service:              imap
    host:                 mail.example.com # Required
    port:                 143
    username:             ~
    password:             ~
    mailbox:              ~

    # either use encryption or not, possible values are tls, ssl or false
    encryption:           tls

    # validate server certificate or not
    validate-cert:        true
```

Usage
-----

A connection can be obtained trough the `abimus_fetch` service from the container.

Controller example:

``` php
/** @var $connection \Abimus\FetchBundle\ConnectionInterface */
$connection = $this->get('abimus_fetch')->getConnection();
$messages = $connection->getMessages();

/** @var $message \Fetch\Message */
foreach ($messages as $message) {
    echo "Subject: {$message->getSubject()}\nBody: {$message->getMessageBody()}\n";
}
```

Further message handling is the same as with the
[Fetch library](http://github.com/tedivm/Fetch) itself.

Multiple Connections
--------------------

If you want to configure multiple connections in YAML, put them under the `connections`
key and give them a unique name:

``` yaml
abimus_fetch:
    default_connection: default
    connections:
        default:
            host: mail.example.com
        special:
            host: mail.special.com
            port: 1234
            encryption: ssl
```

To obtain the given connections, `getConnection()` accepts the connection name as an
argument. If no argument is supplied, the `default_connection` is returned.

``` php
$this->get('abimus_fetch')->getConnection('special');
```

Each connection is also accessible via the `abimus_fetch.[name]_connection` service
where `[name]` is the name of the connection.