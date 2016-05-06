<?php

class Nip_Tool_Generator
{
    protected $_tool;
    protected $_entities;

    public function  __call($name, $arguments)
    {
        if (strpos($name, 'generate') === 0) {
            $name = str_replace('generate', '', $name);
            return $this->getEntityInstance($name)->generate($arguments[0]);
        }
        die('Call to undefiend method ' . __CLASS__);
    }

    public function setTool($tool)
    {
        $this->_tool = $tool;
        return $this;
    }

    /**
     * @return Nip_Tool
     */
    public function getTool()
    {
        return $this->_tool;
    }

    public function getEntityInstance($name) {
        $class = $this->getEntityClass($name);
        if (!$this->_entities[$class]) {
            $this->_entities[$class] = new $class();
            $this->_entities[$class]->setGenerator($this);
        }
        return $this->_entities[$class];
    }

    public function getEntityClass($name)
    {
        return 'Nip_Tool_Generator_Entity_' . ucfirst($name);
    }

    public function newClass() {
        $object = new Nip_Tool_Generator_Class();
        return $object;
    }


}