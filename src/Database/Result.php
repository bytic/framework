<?php

namespace Nip\Database;

use Nip\Database\Adapters\AbstractAdapter;
use Nip\Database\Adapters\MySQLi;
use Nip\Database\Query\AbstractQuery;

/**
 * Class Result.
 */
class Result
{
    /**
     * @var \mysqli_result
     */
    protected $resultSQL;

    /**
     * @var AbstractAdapter| MySQLi
     */
    protected $adapter;

    /**
     * @var AbstractQuery
     */
    protected $query;

    protected $results = [];

    /**
     * Result constructor.
     *
     * @param \mysqli_result  $resultSQL
     * @param AbstractAdapter $adapter
     */
    public function __construct($resultSQL, $adapter)
    {
        $this->resultSQL = $resultSQL;
        $this->adapter = $adapter;
    }

    public function __destruct()
    {
        if ($this->resultSQL && !is_bool($this->resultSQL)) {
            $this->getAdapter()->freeResults($this->resultSQL);
        }
    }

    /**
     * @return AbstractAdapter|MySQLi
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param AbstractAdapter|MySQLi $adapter
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Fetches all rows from current result set.
     *
     * @return array
     */
    public function fetchResults()
    {
        if (count($this->results) == 0) {
            while ($result = $this->fetchResult()) {
                $this->results[] = $result;
            }
        }

        return $this->results;
    }

    /**
     * Fetches row from current result set.
     *
     * @return bool|array
     */
    public function fetchResult()
    {
        if ($this->checkValid()) {
            try {
                return $this->getAdapter()->fetchAssoc($this->resultSQL);
            } catch (Exception $e) {
                $e->log();
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function checkValid()
    {
        if (!$this->isValid()) {
            trigger_error('Invalid result for query ['.$this->getQuery()->getString().']', E_USER_WARNING);

            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->resultSQL !== false && $this->resultSQL !== null;
    }

    /**
     * @return AbstractQuery
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param AbstractQuery $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return bool|int
     */
    public function numRows()
    {
        if ($this->checkValid()) {
            return $this->getAdapter()->numRows($this->resultSQL);
        }

        return false;
    }
}
