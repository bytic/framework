<?php

namespace Nip\Config\FileParser;

/**
 * Class AbstractFileParser
 * @package Nip\Config\FileParser
 */
abstract class AbstractFileParser implements FileParserInterface
{
    /**
     * Path to the config file
     *
     * @var string
     */
    protected $path;

    /**
     * Sets the path to the config file
     *
     * @param string $path
     *
     * @codeCoverageIgnore
     */
    public function __construct($path)
    {
        $this->path = $path;
    }


}
