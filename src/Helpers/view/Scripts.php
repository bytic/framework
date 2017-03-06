<?php

namespace Nip\Helpers\View;

/**
 * Class Scripts
 * @package Nip\Helpers\View
 */
class Scripts extends AbstractHelper
{

    protected $files = [];

    protected $_defaultPlaceholder = "head";

    /**
     * @param $file
     * @param bool $placeholder
     * @return Scripts
     */
    public function add($file, $placeholder = false)
    {
        return $this->addFile($file, 'add', $placeholder);
    }

    /**
     * @param $file
     * @param string $direction
     * @param bool $placeholder
     * @return $this
     */
    public function addFile($file, $direction = 'add', $placeholder = false)
    {
        if (!$placeholder) {
            $placeholder = $this->_defaultPlaceholder;
        }

        if (!is_array($this->files[$placeholder])) {
            $this->files[$placeholder] = [];
        }

        if ($direction == 'prepend') {
            array_unshift($this->files[$placeholder], $file);
        } else {
            $this->files[$placeholder][] = $file;
        }

        return $this;
    }

    /**
     * @param $file
     * @param bool $placeholder
     * @return Scripts
     */
    public function prepend($file, $placeholder = false)
    {
        return $this->addFile($file, 'prepend', $placeholder);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render($this->_defaultPlaceholder);
    }

    /**
     * @param bool $placeholder
     * @return string
     */
    public function render($placeholder = false)
    {
        if (!$placeholder) {
            $placeholder = $this->_defaultPlaceholder;
        }

        return $this->renderHMTL($this->files[$placeholder]);
    }

    /**
     * @param $files
     * @return string
     */
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

            foreach ($internal as $file) {
                $return .= $this->buildTag($this->buildURL($file));
            }
        }

        return $return;
    }

    /**
     * @param $path
     * @return string
     */
    public function buildTag($path)
    {
        return "<script type=\"text/javascript\" src=\"$path\"></script>\r\n";
    }

    /**
     * @param $source
     * @return string
     */
    public function buildURL($source)
    {
        return asset('/scripts' . $source . '.js');
    }
}
