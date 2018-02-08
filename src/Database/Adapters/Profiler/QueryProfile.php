<?php

namespace Nip\Database\Adapters\Profiler;

use Nip\Profiler\Profile;

/**
 * Class QueryProfile
 * @package Nip\Database\Adapters\Profiler
 */
class QueryProfile extends Profile
{
    public $query;
    public $type;
    public $adapter;

    public $info;
    public $affectedRows;
    public $columns = ['time', 'type', 'memory', 'query', 'affectedRows', 'info'];

    /**
     * @param null $name
     */
    public function setName($name)
    {
        $this->query = $name;
        $this->type = $this->detectQueryType();

        parent::setName($name);
    }

    /**
     * @return string
     */
    public function detectQueryType()
    {
        // make sure we have a query type
        switch (strtolower(substr($this->query, 0, 6))) {
            case 'insert':
                return 'INSERT';

            case 'update':
                return 'UPDATE';

            case 'delete':
                return 'DELETE';

            case 'select':
                return 'SELECT';

            default:
                return 'QUERY';
        }
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return mixed
     */
    public function getConnection()
    {
        return $this->query;
    }

    public function calculateResources()
    {
        parent::calculateResources();
        $this->getInfo();
    }

    public function getInfo()
    {
        $this->info = $this->getAdapter()->info();
        $this->affectedRows = $this->getAdapter()->affectedRows();
    }

    /**
     * @return mixed
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param mixed $adapter
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
    }
}
