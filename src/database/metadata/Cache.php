<?php

class Nip_Db_Metadata_Cache extends \Nip\Cache\Manager
{

    protected $_ttl = 10*24*60*60;
    protected $_active = true;
    protected $_metadata;

    public function describeTable($table)
    {
        return $this->get($table);
    }

    public function reload($cacheId)
    {
        return $this->saveData($cacheId, $this->generate($cacheId));
    }

    public function generate($cacheId)
    {
        $data = $this->getMetadata()->getConnection()->describeTable($cacheId);
        $this->_data[$cacheId] = $data;
        return $data;
    }

    public function get($cacheId)
    {
        if (!$this->valid($cacheId)) {
            $this->reload($cacheId);
        }

        return $this->getData($cacheId);
    }

    public function cachePath()
    {
        return parent::cachePath() . '/db-metadata/';
    }

    public function setMetadata($metadata)
    {
        $this->_metadata = $metadata;
        return $this;
    }

    /**
     * @return Nip_Db_Metadata
     */
    public function getMetadata()
    {
        return $this->_metadata;
    }

}