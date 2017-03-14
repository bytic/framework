<?php

namespace Nip\Helpers\View;

use Nip\Utility\Str;

/**
 * Nip Framework
 *
 * @category   Nip
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class StyleSheets extends AbstractHelper
{
    /**
     * @var array
     */
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
        if (!isset($this->files[$condition]) || !is_array($this->files[$condition])) {
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

        if (count($this->files)) {
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
    public function buildTag($path, $condition = null)
    {
        $return = '<link rel="stylesheet" type="text/css" media="screen" href="' . $this->buildURL($path) . '" />';
        if ($condition != null && !empty($condition)) {
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
        if (Str::startsWith($source, ['http', 'https'])) {
            return $source;
        } else {
            return asset('/stylesheets/' . $source . '.css');
        }
    }
}
