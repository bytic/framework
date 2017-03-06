<?php

namespace Nip\Helpers\View;

/**
 * Class TinyMCE
 * @package Nip\Helpers\View
 */
class TinyMCE extends AbstractHelper
{
    /**
     * @var bool
     */
    protected $enabled = false;

    /**
     * @var string
     */
    protected $base = 'tinymce';

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled = true)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return mixed
     */
    public function init()
    {
        if ($this->enabled) {
            $this->getView()->Scripts()
                ->add($this->getBase() . '/jquery.tinymce.min.js', 'tinymce')
                ->add($this->getBase() . '/tinymce.min.js', 'tinymce')
                ->add($this->getBase() . '/init.js', 'tinymce');
        }

        return $this->getView()->Scripts()->render('tinymce');
    }

    /**
     * @return string
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * @param $base
     */
    public function setBase($base)
    {
        $this->base = $base;
    }
}
