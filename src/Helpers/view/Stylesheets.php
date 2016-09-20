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

    protected $_files = [];
    protected $_pack = false;

    public function add($file, $condition = false)
    {
        $this->_files[$condition][$file] = $file;
        return $this;
    }

    public function prepend($file, $condition = false)
    {
        if (!is_array($this->_files[$condition])) {
            $this->_files[$condition] = [];
        }
        array_unshift($this->_files[$condition], $file);
        return $this;
    }

    public function remove($file, $condition = false)
    {
        unset($this->_files[$condition][$file]);
        return $this;
    }

    public function __toString()
    {
        $return = '';

        if ($this->_files) {
            foreach ($this->_files as $condition => $files) {
                if ($this->_pack) {
                    $return .= $this->buildTag($this->pack($files), $condition);
                } else {
                    foreach ($files as $file) {
                        $return .= $this->buildTag($file, $condition);
                    }
                }
            }
        }

        return $return;
    }

    public function buildTag($path, $condition)
    {
        $return = '<link rel="stylesheet" type="text/css" media="screen" href="'.$this->buildURL($path).'" />';
        if ($condition) {
            $return = '<!--[if '.$condition.']>'.$return.'<![endif]-->';
        }
        $return .= "\r\n";

        return $return;
    }

    public function buildURL($source)
    {
        if (preg_match('/https?:\/\//', $source)) {
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
                $path = STYLESHEETS_PATH.$file.".css";
                if (file_exists($path)) {
                    $lastUpdated = max($lastUpdated, filemtime($path));
                }
            }

            $hash = md5(implode("", $files)).".".$lastUpdated;

            $path = CACHE_PATH."stylesheets/".$hash;
            if (!file_exists($path.".css")) {
                $content = "";
                foreach ($files as $file) {
                    $content .= file_get_contents(STYLESHEETS_PATH.$file.".css")."\r\n";
                }

                $css = new csstidy();
                $css->set_cfg('remove_last_;', TRUE);
                $css->load_template('highest_compression');

                $css->parse($content);

                $content = $css->print->plain();

                // Parse content to remove all but one ../ instance
                $content = preg_replace("`url\((\.\.\/){1,}`i", "url(../", $content);

                $file = new Nip_File_Handler(array("path" => $path.".css"));
                $file->write($content);

                if ($file->gzip()) {
                    $file->setPath($path.".gz")->write();
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