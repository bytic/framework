<?php

namespace Nip;

use Nip\AutoLoader\ClassMapGenerator;
use Nip\AutoLoader\Exception as AutoloadException;

function _autoload($class)
{
    $autoloader = AutoLoader::instance();
    $autoloader->autoload($class);
}

spl_autoload_register('Nip\_autoload');

class AutoLoader
{
    protected $_directories = array();
    protected $_map         = array();
    protected $_mapGenerator;
    protected $_cachePath;

    public function __construct()
    {
        $this->_mapGenerator = new ClassMapGenerator();
    }
    /**
     * If true, if a class location is not mapped, it tries again by rewriting the cache file
     */
    protected $_retry   = true;
    protected $_isFatal = true;

    public function addDirectory($dir)
    {
        $this->_directories[] = $dir;
        return $this;
    }

    public function setCachePath($path)
    {
        $this->_cachePath = $path;
        return $this;
    }

    public function isClass($class)
    {
        return is_file($this->getClassLocation($class));
    }

    public function autoload($class)
    {
        try {
            return $this->load($class);
        } catch (AutoloadException $ex) {
            if ($this->isFatal()) {
                trigger_error($ex, E_USER_ERROR);
            }
        }
        return false;
    }

    public function load($class)
    {
        if (class_exists($class, false)) {
            return true;
        }

        $file = $this->getClassLocation($class);
        if ($file) {
            if (include($file)) {
                if (class_exists($class, false) || interface_exists($class,
                        false) || trait_exists($class, false)) {
                    return true;
                } else {
                    throw new AutoloadException("Cannot find the $class class in $file");
                }
            } else {
                throw new AutoloadException("Cannot include $class file $file");
            }
        } else {
            throw new AutoloadException("Cannot find class $class");
        }

        return false;
    }

    protected function getClassLocation($class, $retry = false)
    {
        if (in_array($class, array_keys($this->getMap()))) {
            return $this->_map[$class];
        }

        if (!$this->_retry) {
            return false;
        }

        if (!$retry) {
            \Nip_Profiler::instance()->start('autoloader [buildCache]');
            $this->generateMap();
            \Nip_Profiler::instance()->end('autoloader [buildCache]');

            return $this->getClassLocation($class, true);
        }
        return false;
    }

    public function generateMap()
    {
        foreach ($this->_directories as $dir) {
            $this->generateMapDir($dir);
        }
    }

    public function generateMapDir($dir)
    {
        $fileName = $this->getCacheName($dir);
        $filePath = $this->_cachePath.$fileName;
        $this->_mapGenerator->dump($dir, $filePath);
    }

    protected function getMap()
    {
        if (!$this->_map) {
            foreach ($this->_directories as $dir) {
                $fileName = $this->getCacheName($dir);
                $filePath = $this->_cachePath.$fileName;

                if (!$this->readCacheFile($filePath)) {
                    \Nip_Profiler::instance()->start('autoloader [readCache]');
                    $this->generateMapDir($dir);
                    $this->readCacheFile($filePath);
                    \Nip_Profiler::instance()->start('autoloader [readCache]');
                }
            }
        }
        return $this->_map;
    }

    protected function readCacheFile($filePath)
    {
        if (file_exists($filePath)) {
            $map = require $filePath;
            if (is_array($map)) {
                $this->_map = array_merge($this->_map, $map);
            }
            return true;
        }
        return false;
    }

    public function getCacheName($dir)
    {
        if (defined('ROOT_PATH')) {
            $dir = str_replace(ROOT_PATH, '', $dir);
        }
        return str_replace(DS, '-', $dir).'.php';
    }

    public function setRetry($retry)
    {
        $this->_retry = $retry;
    }

    public function isFatal($value = null)
    {
        if (is_bool($value)) {
            $this->_isFatal = $value;
        }

        return $this->_isFatal == true;
    }

    /**
     * Singleton
     *
     * @return self
     */
    static public function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }
        return $instance;
    }
}