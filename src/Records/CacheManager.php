<?php

namespace Nip\Records;

use Nip\Records\_Abstract\Table as Records;

class CacheManager extends \Nip\Cache\Manager {

    /**
     * @var Records
     */
    protected $_manager;

    public function  __construct()
    {
        $this->_active = (\Nip\FrontController::instance()->getRequest()->getModuleName() == 'default');
    }


    /**
     * @param Records $manager
     * @return $this
     */
    public function setManager($manager)
    {
        $this->_manager = $manager;
        return $this;
    }

    /**
     * @return Records
     */
    public function getManager()
    {
        return $this->_manager;
    }

    public function getCacheId($type = false)
    {
        $cacheId = $this->getManager()->getController() . '-' . $type;
        return $cacheId;
    }

    public function filePath($cacheId) {
        $cacheId = $this->getCacheId($cacheId);
        return $this->cachePath() . $cacheId . '.php';
    }

    public function cachePath() {
        return parent::cachePath() . '/records/';
    }

}