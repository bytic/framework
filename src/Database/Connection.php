<?php

namespace Nip\Database;

use Nip\Database\Adapters\AbstractAdapter;
use Nip\Database\Query\AbstractQuery as AbstractQuery;
use Nip\Database\Query\Select as SelectQuery;

class Connection
{
    protected $_adapter = null;

    protected $_connection;
    protected $_database;
    protected $metadata;
    protected $_query;
    protected $_queries = [];

    /**
     * Connects to SQL server.
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $database
     * @param bool   $newLink
     *
     * @return static
     */
    public function connect($host, $user, $password, $database, $newLink = false)
    {
        if (!$this->_connection) {
            try {
                $this->_connection = $this->getAdapter()->connect($host, $user, $password, $database, $newLink);
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

    public function setAdapter($adapter)
    {
        $this->_adapter = $adapter;
    }

    public function initAdapter()
    {
        $this->setAdapterName('MySQLi');
    }

    public function setAdapterName($name)
    {
        $this->setAdapter($this->newAdapter($name));
    }

    /**
     * @param $name
     *
     * @return AbstractAdapter
     */
    public function newAdapter($name)
    {
        $class = static::getAdapterClass($name);

        return new $class();
    }

    public static function getAdapterClass($name)
    {
        return '\Nip\Database\Adapters\\'.$name;
    }

    public function getDatabase()
    {
        return $this->_database;
    }

    /**
     * @param mixed $database
     */
    public function setDatabase($database)
    {
        $this->_database = $database;
    }

    /**
     * @return Metadata\Manager
     */
    public function getMetadata()
    {
        if (!$this->metadata) {
            $this->metadata = new Metadata\Manager();
            $this->metadata->setConnection($this);
        }

        return $this->metadata;
    }

    /**
     * Prefixes table names.
     *
     * @param string $table
     *
     * @return string
     */
    public function tableName($table)
    {
        return $table;
    }

    /**
     * @param string $type optional
     *
     * @return SelectQuery
     */
    public function newSelect()
    {
        return $this->newQuery('select');
    }

    /**
     * @param string $type optional
     *
     * @return AbstractQuery
     */
    public function newQuery($type = 'select')
    {
        $className = '\Nip\Database\Query\\'.inflector()->camelize($type);
        $query = new $className();
        /* @var AbstractQuery $query */
        $query->setManager($this);

        return $query;
    }

    /**
     * Executes SQL query.
     *
     * @param mixed|AbstractQuery $query
     *
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
     * Gets the ID of the last inserted record.
     *
     * @return int
     */
    public function lastInsertID()
    {
        return $this->getAdapter()->lastInsertID();
    }

    /**
     * Gets the number of rows affected by the last operation.
     *
     * @return int
     */
    public function affectedRows()
    {
        return $this->getAdapter()->affectedRows();
    }

    /**
     * Disconnects from server.
     */
    public function disconnect()
    {
        if ($this->_connection) {
            try {
                $this->getAdapter()->disconnect();
            } catch (Exception $e) {
                $e->log();
            }
        }
    }

    public function describeTable($table)
    {
        return $this->getAdapter()->describeTable($this->protect($table));
    }

    /**
     * Adds backticks to input.
     *
     * @param string $input
     *
     * @return string
     */
    public function protect($input)
    {
        return str_replace('`*`', '*', '`'.str_replace('.', '`.`', $input).'`');
    }

    public function getQueries()
    {
        return $this->_queries;
    }
}
