<?php

namespace Nip\Config\FileParser;

/**
 * Interface FileParserInterface
 * @package Nip\Config\FileParser
 */
interface FileParserInterface
{
    /**
     * Parses a file from `$path` and gets its contents as an array
     *
     * @param  string $path
     *
     * @return array
     */
    public function parse();

    /**
     * Returns an array of allowed file extensions for this parser
     *
     * @return array
     */
    public function getSupportedExtensions();
}
