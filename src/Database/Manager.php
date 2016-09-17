<?php

namespace Nip\Database;

use Nip\Bootstrap as Bootstrap;

class Manager
{

    /**
     * @var Bootstrap
     */
    protected $_bootstrap;
    protected $_connections = [];

    public function newConnectionFromConfig($config)
    {
        $connection = $this->createNewConnection(
            $config->adapter,
            $config->host,
            $config->user,
            $config->password,
            $config->name);
        return $connection;
    }

    public function createNewConnection($adapter, $host, $user, $password, $database)
    {

        try {
            $connection = $this->newConnection();

            $adapter = $connection->newAdapter($adapter);
            $connection->setAdapter($adapter);

            $connection->connect($host, $user, $password, $database);
            $this->initNewConnection($connection);

        } catch (Nip_DB_Exception $e) {
            echo '<h1>Error connecting to database</h1>';
            if (Nip_Staging::instance()->getStage()->inTesting()) {
                echo '<h4>' . $e->getMessage() . '</h4>';
                $e->log();
            }
            die();
        }

        return $connection;
    }

    public function newConnection()
    {
        return new \Nip\Database\Connection();
    }

    public function initNewConnection($connection)
    {
        if ($this->getBootstrap()->getDebugBar()->isEnabled()) {
            $this->getBootstrap()->getDebugBar()->initDatabaseAdapter($connection->getAdapter());
        }
    }

    /**
     * @return Bootstrap
     */
    public function getBootstrap()
    {
        return $this->_bootstrap;
    }

    /**
     * @param Bootstrap $bootstrap
     */
    public function setBootstrap($bootstrap)
    {
        $this->_bootstrap = $bootstrap;
    }

}