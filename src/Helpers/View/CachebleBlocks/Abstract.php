<?php

namespace Nip\Helpers\View\CachebleBlocks;

use Nip\Filesystem\Exception\IOException;
use Nip_File_System as FileSystem;

/**
 * Class AbstractBlock.
 */
class AbstractBlock
{
    protected $_name;

    /** $_model Nip_Record */
    protected $_model;

    protected $_manager;

    protected $_viewPath;

    /**
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->_name = $name;

        return $this;
    }

    /**
     * @param $model
     *
     * @return $this
     */
    public function setModel($model)
    {
        $this->_model = $model;

        return $this;
    }

    /**
     * @param $manager
     *
     * @return $this
     */
    public function setManager($manager)
    {
        $this->_manager = $manager;

        return $this;
    }

    public function setViewPath($path)
    {
        $this->_viewPath = $path;

        return $this;
    }

    public function render()
    {
        $file = $this->filePath();
//        echo gzuncompress(file_get_contents($file));
        readfile($file);
    }

    public function filePath()
    {
        $fileName = str_replace('/', '+', $this->_viewPath);

        return $this->cachePath().$fileName.'.html';
    }

    /**
     * @return mixed
     */
    public function cachePath()
    {
        return $this->_model->getCacheBlocksPath();
    }

    /**
     * @param $ttl
     *
     * @return bool
     */
    public function valid($ttl)
    {
        $ttl = $ttl !== null ? $ttl : $this->_ttl;
        if ($this->exists()) {
            if (!is_int($ttl)) {
                return true;
            }
            $fmtime = filemtime($this->filePath());
            if (($fmtime + $ttl) > time()) {
                return true;
            }
        }

        return false;
    }

    public function exists()
    {
        return file_exists($this->filePath());
    }

    public function regenerate()
    {
        $file = $this->filePath();
        $filesystem = FileSystem::instance();
        $content = $this->_manager->getView()->load($this->_viewPath, [], true);

        $dir = dirname($file);
        if (!is_dir($dir)) {
            $filesystem->createDirectory($dir, 0777);
        }

        $content = $this->_manager->getView()->HTML()->compress($content);
//        $content = gzcompress($content);
        if (file_put_contents($file, $content)) {
            try {
                $filesystem->chmod($file, 0777);
            } catch (IOException $e) {
                // discard chmod failure (some filesystem may not support it)
            }

            return true;
        } else {
            $message = 'Cannot open CachebleBlocks file for writing: ';
            if (app()->get('staging')->getStage()->inTesting()) {
                $message .= ' [ '.$file.' ] ';
            }
            die($message);
        }
    }
}
