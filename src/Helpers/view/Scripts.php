<?php

namespace Nip\Helpers\View;

/**
 * Class Scripts
 * @package Nip\Helpers\View
 */
class Scripts extends AbstractHelper
{

    /**
     * @var array
     */
    protected $files = [];

    protected $defaultPlaceholder = "head";

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
        if ($placeholder === false || empty($placeholder)) {
            $placeholder = $this->defaultPlaceholder;
        }

        if (!isset($this->files[$placeholder]) || !is_array($this->files[$placeholder])) {
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
        return $this->render($this->defaultPlaceholder);
    }

    /**
     * @param bool $placeholder
     * @return string
     */
    public function render($placeholder = false)
    {
        if ($placeholder == false && empty($placeholder)) {
            $placeholder = $this->defaultPlaceholder;
        }

        if (isset($this->files[$placeholder])) {
            return $this->renderHMTL($this->files[$placeholder]);
        }
        return '';
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


            if (count($external)) {
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
        return asset('/scripts/' . $source . '.js');
    }
}
