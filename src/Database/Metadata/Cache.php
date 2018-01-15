<?php

namespace Nip\Database\Metadata;

use Nip\Cache\Manager as CacheManager;
use Nip\Database\Connection;

/**
 * Class Cache.
 */
class Cache extends CacheManager
{
    protected $_metadata;

    public function __construct()
    {
        $this->setTtl(10 * 24 * 60 * 60);
    }

    public function describeTable($table)
    {
        $cacheId = $this->getCacheId($table);

        return $this->get($cacheId);
    }

    public function getCacheId($table)
    {
        return $this->getConnection()->getDatabase().'.'.$table;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->getMetadata()->getConnection();
    }

    /**
     * @return Manager
     */
    public function getMetadata()
    {
        return $this->_metadata;
    }

    public function setMetadata($metadata)
    {
        $this->_metadata = $metadata;

        return $this;
    }

    public function get($cacheId)
    {
        if (!$this->valid($cacheId)) {
            $this->reload($cacheId);
        }

        return $this->getData($cacheId);
    }

    public function reload($cacheId)
    {
        return $this->saveData($cacheId, $this->generate($cacheId));
    }

    public function generate($cacheId)
    {
        $data = $this->getConnection()->describeTable($cacheId);
        $this->_data[$cacheId] = $data;

        return $data;
    }

    public function cachePath()
    {
        return parent::cachePath().'/db-metadata/';
    }
}
