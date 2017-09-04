<?php

namespace Nip\Database\Connections;

use Exception;
use Nip\Database\Adapters\AbstractAdapter;
use Nip\Database\Metadata\Manager as MetadataManager;
use Nip\Database\Query\AbstractQuery as AbstractQuery;
use Nip\Database\Query\Delete as DeleteQuery;
use Nip\Database\Query\Insert as InsertQuery;
use Nip\Database\Query\Select as SelectQuery;
use Nip\Database\Query\Update as UpdateQuery;
use Nip\Database\Result;

/**
 * Class Connection
 * @package Nip\Database
 */
class Connection
{

    /**
     * The active PDO connection.
     *
     * @var PDO
     */
    protected $pdo;

    /**
     * The name of the connected database.
     *
     * @var string
     */
    protected $database;
    /**
     * The table prefix for the connection.
     *
     * @var string
     */
    protected $tablePrefix = '';

    /**
     * The database connection configuration options.
     *
     * @var array
     */
    protected $config = [];

    protected $_adapter = null;

    protected $metadata;

    protected $_query;

    protected $_queries = [];

    /**
     * Create a new database connection instance.
     *
     * @param  \PDO|\Closure $pdo
     * @param  string $database
     * @param  string $tablePrefix
     * @param  array $config
     */
    public function __construct($pdo, $database = '', $tablePrefix = '', $config = [])
    {
        $this->pdo = $pdo;

        // First we will setup the default properties. We keep track of the DB
        // name we are connected to since it is needed when some reflective
        // type commands are run such as checking whether a table exists.
        $this->database = $database;

        $this->tablePrefix = $tablePrefix;
        $this->config = $config;

        // We need to initialize a query grammar and the query post processors
        // which are both very important parts of the database abstractions
        // so we initialize these to their default values while starting.
//        $this->useDefaultQueryGrammar();
//        $this->useDefaultPostProcessor();
    }

    /**
     * Connects to SQL server
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $database
     * @param bool $newLink
     * @return static
     */
    public function connect($host, $user, $password, $database, $newLink = false)
    {
        if (!$this->pdo) {
            try {
                $this->pdo = $this->getAdapter()->connect($host, $user, $password, $database, $newLink);
                $this->setDatabase($database);
            } catch (Exception $e) {
                $e->log();
            }
        }

        return $this;
    }

    /**
     * @return AbstractAdapter
     */
    public function getAdapter()
    {
        if ($this->_adapter == null) {
            $this->initAdapter();
        }

        return $this->_adapter;
    }

    /**
     * @param AbstractAdapter $adapter
     */
    public function setAdapter($adapter)
    {
        $this->_adapter = $adapter;
    }

    public function initAdapter()
    {
        $this->setAdapterName('MySQLi');
    }

    /**
     * @param string $name
     */
    public function setAdapterName($name)
    {
        $this->setAdapter($this->newAdapter($name));
    }

    /**
     * @param $name
     * @return AbstractAdapter
     */
    public function newAdapter($name)
    {
        $class = static::getAdapterClass($name);

        return new $class();
    }

    /**
     * @param $name
     * @return string
     */
    public static function getAdapterClass($name)
    {
        return '\Nip\Database\Adapters\\' . $name;
    }

    /**
     * @return string
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param string $database
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }

    /**
     * @return MetadataManager
     */
    public function getMetadata()
    {
        if (!$this->metadata) {
            $this->metadata = new MetadataManager();
            $this->metadata->setConnection($this);
        }

        return $this->metadata;
    }

    /**
     * Prefixes table names
     *
     * @param string $table
     * @return string
     */
    public function tableName($table)
    {
        return $table;
    }

    /**
     * @return SelectQuery
     */
    public function newSelect()
    {
        return $this->newQuery('select');
    }

    /**
     * @param string $type optional
     * @return AbstractQuery|SelectQuery|UpdateQuery|InsertQuery|DeleteQuery
     */
    public function newQuery($type = "select")
    {
        $className = '\Nip\Database\Query\\' . inflector()->camelize($type);
        $query = new $className();
        /** @var AbstractQuery $query */
        $query->setManager($this);

        return $query;
    }

    /**
     * @return InsertQuery
     */
    public function newInsert()
    {
        return $this->newQuery('insert');
    }

    /**
     * @return UpdateQuery
     */
    public function newUpdate()
    {
        return $this->newQuery('update');
    }

    /**
     * @return DeleteQuery
     */
    public function newDelete()
    {
        return $this->newQuery('delete');
    }

    /**
     * Executes SQL query
     *
     * @param mixed|AbstractQuery $query
     * @return Result
     */
    public function execute($query)
    {
        $this->_queries[] = $query;

        $sql = is_string($query) ? $query : $query->getString();

        $resultSQL = $this->getAdapter()->execute($sql);
        $result = new Result($resultSQL, $this->getAdapter());
        $result->setQuery($query);

        return $result;
    }

    /**
     * Gets the ID of the last inserted record
     * @return int
     */
    public function lastInsertID()
    {
        return $this->getAdapter()->lastInsertID();
    }

    /**
     * Gets the number of rows affected by the last operation
     * @return int
     */
    public function affectedRows()
    {
        return $this->getAdapter()->affectedRows();
    }

    /**
     * Disconnects from server
     */
    public function disconnect()
    {
        if ($this->pdo) {
            try {
                $this->getAdapter()->disconnect();
            } catch (Exception $e) {
                $e->log();
            }
        }
    }

    /**
     * @param null|string $table
     * @return mixed
     */
    public function describeTable($table)
    {
        return $this->getAdapter()->describeTable($this->protect($table));
    }

    /**
     * Adds backticks to input
     *
     * @param string $input
     * @return string
     */
    public function protect($input)
    {
        return str_replace("`*`", "*", '`' . str_replace('.', '`.`', $input) . '`');
    }

    /**
     * @return array
     */
    public function getQueries()
    {
        return $this->_queries;
    }
}
