<?php

class Nip_DB_Wrapper
{

    protected $_adapter;
    protected $_connection;
    protected $_database;
    protected $metadata;
    protected $_query;
    protected $_queries = array();

    /**
     * Instantiates database engine adapter
     *
     * @param string $adapter
     * @param string $db_prefix
     */
    public function __construct($adapter = false, $prefix = false)
    {
        if (is_object($adapter)) {
            $this->_adapter = $adapter;
        } else {
            $adapter = $adapter ? $adapter : 'MySQLi';
            $class = 'Nip_DB_Adapters_' . $adapter;
            $this->_adapter = new $class();
        }

        $this->_prefix = $prefix;
    }

    /**
     * Connects to SQL server
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $database
     * @return Nip_DB_Wrapper
     */
    public function connect($host, $user, $password, $database, $newLink = false)
    {
        if (!$this->_connection) {
            try {
                $this->_connection = $this->getAdapter()->connect($host, $user, $password, $database, $newLink);
                $this->setDatabase($database);
            } catch (Nip_DB_Exception $e) {
                $e->log();
            }
        }
        return $this;
    }

    /**
     * @return \Nip_DB_Adapters_Abstract
     */
    public function getAdapter()
    {
        return $this->_adapter;
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
     * @return \Nip_Db_Metadata
     */
    public function getMetadata()
    {
        if (!$this->metadata) {
            $this->metadata = new Nip_Db_Metadata();
            $this->metadata->setWrapper($this);
        }
        return $this->metadata;
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
     * @param string $type optional
     * @return \Nip\Database\Query\_Abstract
     */
    public function newQuery($type = "select")
    {
        $className = 'Nip_DB_Query_' . inflector()->camelize($type);
        $query = new $className;
        $query->setManager($this);

        return $query;
    }

    /**
     * Executes SQL query
     *
     * @param mixed $query
     * @return \Nip_DB_Result
     */
    public function execute($query)
    {
        $this->_queries[] = $query;

        $query = (string)$query;
        $query = $this->getAdapter()->execute($query);
        $result = new Nip_DB_Result($query, $this->getAdapter());

        return $result;
    }

    /**
     * Gets the ID of the last inserted record
     * @return numeric
     */
    public function lastInsertID()
    {
        return $this->getAdapter()->lastInsertID();
    }

    /**
     * Gets the number of rows affected by the last operation
     * @return numeric
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
        if ($this->_connection) {
            try {
                $this->getAdapter()->disconnect();
            } catch (Nip_DB_Exception $e) {
                $e->log();
            }
        }
    }

    public function describeTable($table)
    {
        return $this->getAdapter()->describeTable($this->protect($table));
    }

    public function getQueries()
    {
        return $this->_queries;
    }

    /**
     * Singleton
     *
     * @param string $adapter
     * @param string $prefix
     * @return Nip_DB_Wrapper
     */
    static public function instance($adapter = false, $prefix = false)
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self($adapter, $prefix);
        }
        return $instance;
    }

}