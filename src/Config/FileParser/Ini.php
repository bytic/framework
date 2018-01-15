<?php

namespace Nip\Config\FileParser;

use Nip\Config\Exception\ParseException;

/**
 * Class Ini.
 */
class Ini extends AbstractFileParser
{
    /**
     * {@inheritdoc}
     * Parses an INI file as an array.
     *
     * @throws ParseException If there is an error parsing the INI file
     */
    public function parse()
    {
        if (defined('INI_SCANNER_TYPED')) {
            $data = parse_ini_file($this->getPath(), true, INI_SCANNER_TYPED);
        } else {
            $data = parse_ini_file($this->getPath(), true);
        }
        if ($data === false) {
            $error = error_get_last();

            throw new ParseException($error);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedExtensions()
    {
        return ['ini'];
    }
}
