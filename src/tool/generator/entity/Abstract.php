<?php

class Nip_Tool_Generator_Entity_Abstract
{

    protected $_generator;
    protected $_name;

    public function setGenerator($generator)
    {
        $this->_generator = $generator;
        return $this;
    }

    /**
     * @return Nip_Tool_Generator
     */
    public function getGenerator()
    {
        return $this->_generator;
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


    /**
     * @return Nip_Tool_Console
     */
    public function getConsole()
    {
        return $this->getGenerator()->getTool()->getConsole();
    }


    public function generate($name)
    {
    }
    
}