<?php

class Nip_Db_Metadata_Cache extends Nip_Cache_Manager {

    protected $_active = true;
    protected $_metadata;

    public function describeTable($table) {
        return $this->get($table);
    }

    public function reload($cacheId) {
        return $this->saveData($cacheId, $this->generate($cacheId));
    }

    public function generate($cacheId) {
        $file = $this->filePath($cacheId);
        $data = $this->getMetadata()->getWrapper()->describeTable($cacheId);
        $this->_data[$cacheId] = $data;
        return $data;
    }

    public function get($cacheId) {
        if (!$this->valid($cacheId)) {
            $this->reload($cacheId);
        }

        return $this->getData($cacheId);
    }

    public function cachePath() {
        return parent::cachePath() . '/db-metadata/';
    }

    public function setMetadata($metadata) {
        $this->_metadata = $metadata;
        return $this;
    }

    /**
     * @return Nip_Db_Metadata
     */
    public function getMetadata() {
        return $this->_metadata;
    }

}