<?php

namespace Nip\Database;

use InvalidArgumentException;
use Nip\Application;
use Nip\Database\Connections\Connection;
use Nip\Database\Connections\ConnectionFactory;
use Nip_Helper_Arrays;

/**
 * Class DatabaseManager
 * @package Nip\Database
 */
class DatabaseManager
{

    /**
     * @var Application
     */
    protected $application;

    /**
     * The database connection factory instance.
     *
     * @var ConnectionFactory
     */
    protected $factory;

    protected $connections = [];

    /**
     * DatabaseManager constructor.
     * @param Application $application
     * @param ConnectionFactory $factory
     */
    public function __construct(Application $application, ConnectionFactory $factory)
    {
        $this->application = $application;
        $this->factory = $factory;
    }

    /**
     * Get a database connection instance.
     *
     * @param  string $name
     * @return Connection
     */
    public function connection($name = null)
    {
        list($database, $type) = $this->parseConnectionName($name);
        $name = $name ?: $database;

        // If we haven't created this connection, we'll create it based on the config
        // provided in the application. Once we've created the connections we will
        // set the "fetch mode" for PDO which determines the query return types.
        if (!isset($this->connections[$name])) {
            $this->connections[$name] = $this->configure(
                $connection = $this->makeConnection($name),
                $type
            );
        }
        return $this->connections[$name];
    }

    /**
     * Parse the connection into an array of the name and read / write type.
     *
     * @param  string $name
     * @return string
     */
    protected function parseConnectionName($name)
    {
        $name = $name ?: $this->getDefaultConnection();
        return $name;
    }

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection()
    {
        return $this->application->get('config')->get('database.default');
    }

    /**
     * Prepare the database connection instance.
     *
     * @param  Connection $connection
     * @param  string $type
     * @return Connection
     */
    protected function configure(Connection $connection, $type)
    {
//        $connection = $this->setPdoForType($connection, $type);
        // First we'll set the fetch mode and a few other dependencies of the database
        // connection. This method basically just configures and prepares it to get
        // used by the application. Once we're finished we'll return it back out.
//        if ($this->app->bound('events')) {
//            $connection->setEventDispatcher($this->app['events']);
//        }
        // Here we'll set a reconnector callback. This reconnector can be any callable
        // so we will set a Closure to reconnect from this manager with the name of
        // the connection, which will allow us to reconnect from the connections.
//        $connection->setReconnector(function ($connection) {
//            $this->reconnect($connection->getName());
//        });
        return $connection;
    }

    /**
     * Make the database connection instance.
     *
     * @param  string $name
     * @return Connection
     */
    protected function makeConnection($name)
    {
        $config = $this->configuration($name);

        // First we will check by the connection name to see if an extension has been
        // registered specifically for that connection. If it has we will call the
        // Closure and pass it the config allowing it to resolve the connection.
//        if (isset($this->extensions[$name])) {
//            return call_user_func($this->extensions[$name], $config, $name);
//        }

        // Next we will check to see if an extension has been registered for a driver
        // and will call the Closure if so, which allows us to have a more generic
        // resolver for the drivers themselves which applies to all connections.
//        if (isset($this->extensions[$driver = $config['driver']])) {
//            return call_user_func($this->extensions[$driver], $config, $name);
//        }
        return $this->factory->make($config, $name);
    }

    /**
     * Get the configuration for a connection.
     *
     * @param  string $name
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function configuration($name)
    {
        $name = $name ?: $this->getDefaultConnection();

        // To get the database connection configuration, we will just pull each of the
        // connection configurations and get the configurations for the given name.
        // If the configuration doesn't exist, we'll throw an exception and bail.

        $connections = config('database.connections');
        if (is_null($config = Nip_Helper_Arrays::get($connections, $name))) {
            throw new InvalidArgumentException("Database [$name] not configured.");
        }
        return $config;
    }

    /**
     * @param $config
     * @return Connection
     */
    public function newConnectionFromConfig($config)
    {
        $connection = $this->createNewConnection(
            $config->adapter,
            $config->host,
            $config->user,
            $config->password,
            $config->name
        );
        return $connection;
    }

    /**
     * @return array
     */
    public function getConnections(): array
    {
        return $this->connections;
    }

    /**
     * @return Connection
     */
    public function newConnection()
    {
        return new Connection();
    }

    /**
     * @param $connection
     */
    public function initNewConnection($connection)
    {
//        if ($this->getBootstrap()->getDebugBar()->isEnabled()) {
//            $this->getBootstrap()->getDebugBar()->initDatabaseAdapter($connection->getAdapter());
//        }
    }

//    /**
//     * @return Bootstrap
//     */
//    public function getBootstrap()
//    {
//        return $this->bootstrap;
//    }
//
//    /**
//     * @param Bootstrap $bootstrap
//     */
//    public function setBootstrap($bootstrap)
//    {
//        $this->bootstrap = $bootstrap;
//    }
}
