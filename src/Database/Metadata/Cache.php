<?php

namespace Nip\Database\Metadata;

use Nip\Cache\Manager as CacheManager;
use Nip\Database\Connections\Connection;

/**
 * Class Cache
 * @package Nip\Database\Metadata
 */
class Cache extends CacheManager
{
    protected $ttl = 10 * 24 * 60 * 60;
    protected $active = true;
    protected $metadata;

    /**
     * @param $table
     * @return mixed
     */
    public function describeTable($table)
    {
        $cacheId = $this->getCacheId($table);

        return $this->get($cacheId);
    }

    /**
     * @param $table
     * @return string
     */
    public function getCacheId($table)
    {
        return $this->getConnection()->getDatabase() . '.' . $table;
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
        return $this->metadata;
    }

    /**
     * @param $metadata
     * @return $this
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * @param $cacheId
     * @return mixed
     */
    public function get($cacheId)
    {
        if (!$this->valid($cacheId)) {
            $this->reload($cacheId);
        }

        return $this->getData($cacheId);
    }

    /**
     * @param $cacheId
     * @return mixed
     */
    public function reload($cacheId)
    {
        return $this->saveData($cacheId, $this->generate($cacheId));
    }

    /**
     * @param $cacheId
     * @return mixed
     */
    public function generate($cacheId)
    {
        $data = $this->getConnection()->describeTable($cacheId);
        $this->data[$cacheId] = $data;

        return $data;
    }

    /**
     * @return string
     */
    public function cachePath()
    {
        return parent::cachePath() . '/db-metadata/';
    }
}
