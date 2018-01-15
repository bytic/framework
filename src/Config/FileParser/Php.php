<?php

namespace Nip\Config\FileParser;

use Exception;
use Nip\Config\Exception\ParseException;
use Nip\Config\Exception\UnsupportedFormatException;

/**
 * Class Php.
 */
class Php extends AbstractFileParser
{
    /**
     * {@inheritdoc}
     * Loads a PHP file and gets its' contents as an array.
     *
     * @throws ParseException             If the PHP file throws an exception
     * @throws UnsupportedFormatException If the PHP file does not return an array
     */
    public function parse()
    {
        $path = $this->getPath();
        // Require the file, if it throws an exception, rethrow it
        try {
            /** @noinspection PhpIncludeInspection */
            $temp = require $path;
        } catch (Exception $exception) {
            throw new ParseException(
                [
                    'message'   => 'PHP file threw an exception',
                    'exception' => $exception,
                ]
            );
        }

        // If we have a callable, run it and expect an array back
        if (is_callable($temp)) {
            $temp = call_user_func($temp);
        }

        // Check for array, if its anything else, throw an exception
        if (!is_array($temp)) {
            throw new UnsupportedFormatException('PHP file does not return an array');
        }

        return $temp;
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedExtensions()
    {
        return ['php'];
    }
}
