<?php

class Nip_Tool_Generator_Class_Property
{
    
    protected $_class;

    protected $_name;
    protected $_scope;
    protected $_value;


    public function setClass($class)
    {
        $this->_class = $class;
        return $this;
    }

    /**
     * @return Nip_Tool_Generator_Class
     */
    public function getClass()
    {
        return $this->_class;
    }


    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setScope($scope)
    {
        $this->_scope = $scope;
        return $this;
    }

    public function getScope()
    {
        return $this->_scope;
    }

    public function setValue($value)
    {
        $this->_value = $value;
        return $this;
    }

    public function getValue()
    {
        return $this->_value;
    }



    public function generate()
    {
        $this->_content = $this->getClass()->getTab();
        $this->_content .= $this->getScope() ? $this->getScope() . ' ' : '';
        $this->_content .= '$'. $this->getName();
        $this->_content .= $this->getValue() ? ' = ' . $this->getValue() : '';
        $this->_content .= ';' . "\n";

        return $this->_content;
    }

}