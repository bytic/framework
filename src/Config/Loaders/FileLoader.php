<?php

namespace Nip\Config\Loaders;

use Nip\Config\Exception\RuntimeException;
use Nip\Config\FileParser\AbstractFileParser;

/**
 * Class FileLoader
 * @package Nip\Config\Loaders
 */
class FileLoader extends AbstractLoader
{

    /**
     * Registered config file extensions.
     * key is extension, value is reader instance or plugin name
     *
     * @var array
     */
    protected static $extensions = [
        'ini' => 'Ini',
        'php' => 'Php',
        'json' => 'Json',
        'xml' => 'Xml',
        'yaml' => 'Yaml',
    ];
    /**
     * @var
     */
    protected $extension = null;
    /**
     * @var AbstractFileParser[]
     */
    protected $fileParsers = [];

    /**
     * @return mixed
     */
    protected function getData()
    {
        $type = self::$extensions[$this->getExtension()];

        return $this->getFileParser($type)->setPath($this->getResolvedPath())->parse();
    }

    /**
     * @return mixed
     */
    public function getExtension()
    {
        if ($this->extension === null) {
            $this->initExtension();
        }

        return $this->extension;
    }

    /**
     * @param string $extension
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    public function initExtension()
    {
        $filename = $this->getResolvedPath();
        $pathInfo = pathinfo($filename);
        if (!isset($pathInfo['extension'])) {
            throw new RuntimeException(sprintf(
                'Filename "%s" is missing an extension and cannot be auto-detected',
                $filename
            ));
        }

        $this->setExtension(strtolower($pathInfo['extension']));
    }

    /**
     * @param $type
     * @return AbstractFileParser
     */
    protected function getFileParser($type)
    {
        if (!isset($this->fileParsers[$type])) {
            $this->initFileParser($type);
        }

        return $this->fileParsers[$type];
    }

    /**
     * @param $type
     */
    protected function initFileParser($type)
    {
        $class = 'Nip\Config\FileParser\\' . $type;
        $parser = new $class();
        $this->fileParsers[$type] = $parser;
    }

    /**
     * @throws RuntimeException
     */
    protected function resolvePath()
    {
        $filename = $this->getPath();
        if (file_exists($filename)) {
            return $filename;
        }

        if (!$this->useIncludePath()) {
            throw new RuntimeException(sprintf(
                'Filename "%s" cannot be found relative to the working directory',
                $filename
            ));
        }

        $fromIncludePath = stream_resolve_include_path($filename);
        if (!$fromIncludePath) {
            throw new RuntimeException(sprintf(
                'Filename "%s" cannot be found relative to the working directory or the include_path ("%s")',
                $filename,
                get_include_path()
            ));
        }

        return $fromIncludePath;
    }
}
