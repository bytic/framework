<?php

namespace Nip\Cache;

use Nip\Filesystem\Exception\IOException;
use Nip_File_System as FileSystem;

/**
 * Class Manager
 * @package Nip\Cache
 */
class Manager
{
    protected $active = false;

    protected $ttl = 180;

    protected $data;

    /**
     * @param $cacheId
     * @return mixed
     */
    public function get($cacheId)
    {
        if (!$this->valid($cacheId)) {
            return null;
        }

        return $this->getData($cacheId);
    }

    /**
     * @param $cacheId
     * @return bool
     */
    public function valid($cacheId)
    {
        if ($this->isActive() && $this->exists($cacheId)) {
            $fmtime = filemtime($this->filePath($cacheId));
            if (($fmtime + $this->ttl) > time()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param $active
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @param $cacheId
     * @return bool
     */
    public function exists($cacheId)
    {
        return file_exists($this->filePath($cacheId));
    }

    /**
     * @param $cacheId
     * @return string
     */
    public function filePath($cacheId)
    {
        return $this->cachePath() . $cacheId . '.php';
    }

    /**
     * @return string
     */
    public function cachePath()
    {
        return app('path.storage') . DIRECTORY_SEPARATOR . 'cache';
    }

    /**
     * @param $cacheId
     * @return mixed
     */
    public function getData($cacheId)
    {
        if (!isset($this->data[$cacheId])) {
            $this->data[$cacheId] = $this->loadData($cacheId);
        }

        return $this->data[$cacheId];
    }

    /**
     * @param $cacheId
     * @param bool $retry
     * @return bool|mixed
     */
    public function loadData($cacheId, $retry = true)
    {
        $file = $this->filePath($cacheId);
        $content = file_get_contents($file);
        $data = unserialize($content);

        if (!$data) {
            if ($retry === false) {
                return false;
            }
            $this->reload($cacheId);

            return $this->loadData($cacheId, false);
        }

        return $data;
    }

    /**
     * @param $cacheId
     */
    public function reload($cacheId)
    {
    }

    /**
     * @param $cacheId
     * @param $data
     * @return $this
     */
    public function set($cacheId, $data)
    {
        $this->data[$cacheId] = $data;

        return $this;
    }

    /**
     * @param $cacheId
     * @param $data
     * @return bool
     */
    public function saveData($cacheId, $data)
    {
        $file = $this->filePath($cacheId);
        $content = serialize($data);

        return $this->save($file, $content);
    }

    /**
     * @param $file
     * @param $content
     * @return bool
     */
    public function save($file, $content)
    {
        $dir = dirname($file);
        $filesystem = FileSystem::instance();

        if (!is_dir($dir)) {
            $filesystem->createDirectory($dir, 0777);
        }

        if (file_put_contents($file, $content)) {
            try {
                $filesystem->chmod($file, 0777);
            } catch (IOException $e) {
                // discard chmod failure (some filesystem may not support it)
            }

            return true;
        } else {
            $message = "Cannot open cache file for writing: ";
            //			if (!Nip_Staging::instance()->isPublic()) {
            //				$message .= " [ ".$this->cache_file." ] ";
            //			}
            die($message);
        }
    }

    /**
     * @param $cacheId
     */
    public function delete($cacheId)
    {
        $file = $this->filePath($cacheId);
        if (is_file($file)) {
            unlink($file);
        }
    }
}
