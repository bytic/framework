<?php

namespace Nip\Helpers\View;

class TinyMCE extends AbstractHelper
{

    protected $_enabled = false;

    public function setEnabled($enabled = true)
    {
        $this->_enabled = $enabled;
    }

    public function init()
    {
        if ($this->_enabled) {
            $this->getView()->Scripts()->setPack(false)
                ->add('tinymce/jquery.tinymce.min', 'tinymce')
                ->add('tinymce/tinymce.min', 'tinymce')
                ->add('tinymce/init', 'tinymce');
        }

        return $this->getView()->Scripts()->render('tinymce');
    }

}