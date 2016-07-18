<?php

namespace Nip\Database\Adapters\Profiler;

use Nip\Profiler\Profile;

class QueryProfile extends Profile
{
    public $query;
    public $adapter;

    public $info;
    public $affectedRows;
    public $columns = array('time', 'type', 'memory', 'query', 'affectedRows', 'info');

    public function __construct($query, $queryType)
    {
        $this->query = $query;

        parent::__construct($queryType);
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getConnection()
    {
        return $this->query;
    }

    public function getInfo($adapter)
    {
        $this->info = $adapter->info();
        $this->affectedRows = $adapter->affectedRows();
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