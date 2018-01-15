<?php

namespace Nip\Helpers\View;

class Scripts extends AbstractHelper
{
    protected $_files = [];
    protected $_defaultPlaceholder = 'head';
    protected $_pack = false;

    public function add($file, $placeholder = false)
    {
        return $this->addFile($file, 'add', $placeholder);
    }

    public function addFile($file, $direction = 'add', $placeholder = false)
    {
        if (!$placeholder) {
            $placeholder = $this->_defaultPlaceholder;
        }

        if (!is_array($this->_files[$placeholder])) {
            $this->_files[$placeholder] = [];
        }

        if ($direction == 'prepend') {
            array_unshift($this->_files[$placeholder], $file);
        } else {
            $this->_files[$placeholder][] = $file;
        }

        return $this;
    }

    public function prepend($file, $placeholder = false)
    {
        return $this->addFile($file, 'prepend', $placeholder);
    }

    public function __toString()
    {
        return $this->render($this->_defaultPlaceholder);
    }

    public function render($placeholder = false)
    {
        if (!$placeholder) {
            $placeholder = $this->_defaultPlaceholder;
        }

        return $this->renderHMTL($this->_files[$placeholder]);
    }

    public function renderHMTL($files)
    {
        $return = '';
        if (is_array($files)) {
            $internal = [];
            $external = [];

            foreach ($files as $file) {
                if (preg_match('/https?:\/\//', $file)) {
                    $external[] = $file;
                } else {
                    $internal[] = $file;
                }
            }

            if ($external) {
                foreach ($external as $file) {
                    $return .= $this->buildTag($file);
                }
            }

            $return .= $this->pack($internal);
        }

        return $return;
    }

    public function buildTag($path)
    {
        return "<script type=\"text/javascript\" src=\"$path\"></script>\r\n";
    }

    public function pack($files)
    {
        if ($files) {
            if ($this->_pack === false) {
                $return = '';

                foreach ($files as $file) {
                    $return .= $this->buildTag($this->buildURL($file));
                }

                return $return;
            } else {
                $lastUpdated = 0;
                foreach ($files as $file) {
                    $path = $this->getBasePath().$file.'.js';
                    if (file_exists($path)) {
                        $lastUpdated = max($lastUpdated, filemtime($path));
                    }
                }

                $hash = md5(implode('', $files)).'.'.$lastUpdated;

                $path = CACHE_PATH.'scripts/'.$hash;
                if (!file_exists($path.'.js')) {
                    $content = '';
                    foreach ($files as $file) {
                        $content .= file_get_contents($this->getBasePath().$file.'.js')."\r\n";
                    }
                    $packer = new \JavaScriptPacker($content, 'Normal', true, false);
                    $content = $packer->pack();

                    $file = new \Nip_File_Handler(['path' => $path.'.js']);
                    $file->write($content);

                    if ($file->gzip()) {
                        $file->setPath($path.'.gz')->write();
                    }
                }

                return '<script type="text/javascript" src="'.$this->buildURL($hash).'"></script>'."\r\n";
            }
        }

        return false;
    }

    public function buildURL($source)
    {
        return $this->getBaseUrl().$source.(in_array(\Nip_File_System::instance()->getExtension($source),
            ['js', 'php']) ? '' : '.js');
    }

    public function getBaseUrl()
    {
        return SCRIPTS_URL;
    }

    public function getBasePath()
    {
        return SCRIPTS_PATH;
    }

    public function setPack($pack = true)
    {
        $this->_pack = $pack;

        return $this;
    }
}
