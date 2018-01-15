<?php

namespace Nip\Config\FileParser;

/**
 * Class AbstractFileParser.
 */
abstract class AbstractFileParser implements FileParserInterface
{
    /**
     * Path to the config file.
     *
     * @var string
     */
    protected $path;

    /**
     * Sets the path to the config file.
     *
     * @param string $path
     *
     * @codeCoverageIgnore
     */
    public function __construct($path = null)
    {
        $this->setPath($path);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }
}
