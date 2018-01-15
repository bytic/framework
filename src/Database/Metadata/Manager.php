<?php

namespace Nip\Database\Metadata;

class Manager
{
    protected $_connection;
    protected $_cache;

    public function setConnection($wrapper)
    {
        $this->_connection = $wrapper;

        return $this;
    }

    /**
     * @return \Nip\Database\Connection
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    public function describeTable($table)
    {
        $data = $this->getCache()->describeTable($table);
        if (!is_array($data)) {
            return trigger_error("Cannot load metadata for table [$table]", E_USER_ERROR);
        }

        return $data;
    }

    public function getCache()
    {
        if (!$this->_cache) {
            $this->_cache = new Cache();
            $this->_cache->setMetadata($this);
        }

        return $this->_cache;
    }
}
