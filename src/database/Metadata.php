<?php

class Nip_Db_Metadata {

    protected $_wrapper;
    protected $_cache;

    public function setWrapper($wrapper) {
        $this->_wrapper = $wrapper;
        return $this;
    }

    /**
     * @return Nip_Db_Wrapper
     */
    public function getWrapper() {
        return $this->_wrapper;
    }

    public function describeTable($table) {
        $data = $this->getCache()->describeTable($table);
        if ($data) {
            return $data;
        }
        trigger_error("Cannot load metadata for table [$table]", E_USER_ERROR);
    }

    public function getCache() {
        if (!$this->_cache) {
            $this->_cache = new Nip_Db_Metadata_Cache();
            $this->_cache->setMetadata($this);
        }

        return $this->_cache;
    }
}