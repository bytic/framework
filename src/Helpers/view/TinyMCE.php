<?php

namespace Nip\Helpers\View;

class TinyMCE extends AbstractHelper
{
    protected $_enabled = false;
    protected $_base = 'tinymce';

    public function setEnabled($enabled = true)
    {
        $this->_enabled = $enabled;
    }

    public function init()
    {
        if ($this->_enabled) {
            $this->getView()->Scripts()->setPack(false)
                ->add($this->getBase().'/jquery.tinymce.min.js', 'tinymce')
                ->add($this->getBase().'/tinymce.min.js', 'tinymce')
                ->add($this->getBase().'/init.js', 'tinymce');
        }

        return $this->getView()->Scripts()->render('tinymce');
    }

    public function getBase()
    {
        return $this->_base;
    }

    public function setBase($base)
    {
        $this->_base = $base;
    }
}
