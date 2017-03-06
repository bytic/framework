<?php

namespace Nip\Helpers\View;

/**
 * Nip Framework
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version    SVN: $Id: Stylesheets.php 138 2009-05-27 17:05:36Z victor.stanciu $
 */
class StyleSheets extends AbstractHelper
{

    protected $files = [];

    /**
     * @param $file
     * @param bool $condition
     * @return $this
     */
    public function add($file, $condition = false)
    {
        $this->files[$condition][$file] = $file;
        return $this;
    }

    /**
     * @param $file
     * @param bool $condition
     * @return $this
     */
    public function prepend($file, $condition = false)
    {
        if (!is_array($this->files[$condition])) {
            $this->files[$condition] = [];
        }
        array_unshift($this->files[$condition], $file);
        return $this;
    }

    /**
     * @param $file
     * @param bool $condition
     * @return $this
     */
    public function remove($file, $condition = false)
    {
        unset($this->files[$condition][$file]);
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $return = '';

        if ($this->files) {
            foreach ($this->files as $condition => $files) {
                foreach ($files as $file) {
                    $return .= $this->buildTag($file, $condition);
                }
            }
        }

        return $return;
    }

    /**
     * @param $path
     * @param $condition
     * @return string
     */
    public function buildTag($path, $condition)
    {
        $return = '<link rel="stylesheet" type="text/css" media="screen" href="' . $this->buildURL($path) . '" />';
        if ($condition) {
            $return = '<!--[if ' . $condition . ']>' . $return . '<![endif]-->';
        }
        $return .= "\r\n";

        return $return;
    }

    /**
     * @param $source
     * @return string
     */
    public function buildURL($source)
    {
        if (preg_match('/https?:\/\//', $source)) {
            return $source;
        } else {
            return asset('/stylesheets' . $source . '.css');
        }
    }
}
