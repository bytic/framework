<?php

namespace Nip\Records;

use Nip\Records\AbstractModels\RecordManager as Records;

/**
 * Class CacheManager
 * @package Nip\Records
 */
class CacheManager extends \Nip\Cache\Manager
{

    /**
     * @var Records
     */
    protected $_manager;

    /**
     * CacheManager constructor.
     */
    public function __construct()
    {
        $this->_active = (request()->getModuleName() == 'default');
    }

    /**
     * @param $cacheId
     * @return string
     */
    public function filePath($cacheId)
    {
        $cacheId = $this->getCacheId($cacheId);

        return $this->cachePath().$cacheId.'.php';
    }

    /**
     * @param bool $type
     * @return string
     */
    public function getCacheId($type = false)
    {
        $cacheId = $this->getManager()->getController().'-'.$type;

        return $cacheId;
    }

    /**
     * @return Records
     */
    public function getManager()
    {
        return $this->_manager;
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
     * @return string
     */
    public function cachePath()
    {
        return parent::cachePath().'/records/';
    }
}