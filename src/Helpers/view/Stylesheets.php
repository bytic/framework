<?php

namespace Nip\Helpers\View;

use Nip\Utility\Str;

/**
 * Nip Framework
 *
 * @category   Nip
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class Stylesheets extends AbstractHelper
{
    /**
     * @var array
     */
    protected $files = [];
    protected $_pack = false;


    protected $rawStyles = [];

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
     * @param $content
     * @return $this
     */
    public function addRaw($content)
    {
        $this->rawStyles[] = $content;

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
     * @return string
     */
    public function renderRaw()
    {
        $return = '';
        if (count($this->rawStyles)) {
            $return .= '<style type="text/css" media="screen">';
            $return .= implode("\r\n", $this->rawStyles);
            $return .= '</style>';
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
        $return = '<link rel="stylesheet" type="text/css" media="screen" href="'.$this->buildURL($path).'" />';
        if ($condition != null && !empty($condition)) {
            $return = '<!--[if '.$condition.']>'.$return.'<![endif]-->';
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
            return STYLESHEETS_URL.$source.'.css';
        }
    }

    public function pack($files)
    {
        if ($files) {
            $lastUpdated = 0;
            foreach ($files as $file) {
                $path = STYLESHEETS_PATH.$file.'.css';
                if (file_exists($path)) {
                    $lastUpdated = max($lastUpdated, filemtime($path));
                }
            }

            $hash = md5(implode('', $files)).'.'.$lastUpdated;

            $path = CACHE_PATH.'stylesheets/'.$hash;
            if (!file_exists($path.'.css')) {
                $content = '';
                foreach ($files as $file) {
                    $content .= file_get_contents(STYLESHEETS_PATH.$file.'.css')."\r\n";
                }

                $css = new csstidy();
                $css->set_cfg('remove_last_;', true);
                $css->load_template('highest_compression');

                $css->parse($content);

                $content = $css->print->plain();

                // Parse content to remove all but one ../ instance
                $content = preg_replace("`url\((\.\.\/){1,}`i", 'url(../', $content);

                $file = new Nip_File_Handler(['path' => $path.'.css']);
                $file->write($content);

                if ($file->gzip()) {
                    $file->setPath($path.'.gz')->write();
                }
            }

            return $this->buildURL($hash);
        }

        return false;
    }

    public function setPack($pack = true)
    {
        $this->_pack = $pack;

        return $this;
    }
}
