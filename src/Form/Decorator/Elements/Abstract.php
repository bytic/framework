<?php
abstract class Nip_Form_Decorator_Elements_Abstract
{
    const APPEND  = 'APPEND';
    const PREPEND = 'PREPEND';

    protected $_name;

    protected $_element;
    protected $_placement = 'APPEND';

    /**
     * Separator between new content and old
     * @var string
     */
    protected $_separator = PHP_EOL;

    public function getName()
    {
        if (!$this->_name) {
            $class = get_class($this);
            $class = str_replace('Nip_Form_Decorator_Elements_', '', $class);
            $this->_name = $class;
        }
        return $this->_name;
    }


    public function setElement(Nip_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this;
    }

    /**
     * @return Nip_Form_Element_Abstract
     */
    public function getElement()
    {
        return $this->_element;
    }

    public function setSeparator($separator)
    {
        $this->_separator = $separator;
        return $this;
    }

    public function getSeparator()
    {
        return $this->_separator;
    }

    public function render($content)
    {
        $decorator = $this->generate();
        switch ($this->_placement) {
            case self::PREPEND:
                return $decorator . $this->getSeparator() . $content;
            case self::APPEND:
            default:
                return $content . $this->getSeparator() . $decorator;
        }
    }

    public function generate()
    {
    }
}
