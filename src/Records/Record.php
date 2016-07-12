<?php

class Nip_Record extends \Nip\Records\_Abstract\Row
{

    protected $_dbData = array();
    protected $_helpers = array();

    /**
     * Overloads Ucfirst() helper
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if ($name === ucfirst($name)) {
            $class = 'Nip_Helper_' . $name;

            if (!isset($this->helpers[$class])) {
                $this->_helpers[$class] = new $class;
            }
            return $this->_helpers[$class];
        }

        trigger_error("Call to undefined method $name", E_USER_ERROR);
    }

    public function writeDBData($data = false)
    {
        foreach ($data as $key => $value) {
            $this->_dbData[$key] = $value;
        }
    }

    public function getPrimaryKey()
    {
        $pk = $this->getManager()->getPrimaryKey();
        return $this->$pk;
    }

    public function insert()
    {
        $pk = $this->getManager()->getPrimaryKey();
        $this->$pk = $this->getManager()->insert($this);
        return $this->$pk > 0;
    }

    public function update()
    {
        $return = $this->getManager()->update($this);
        return $return;
    }

    public function save()
    {
        $this->getManager()->save($this);
    }

    public function saveRecord()
    {
        $this->getManager()->save($this);
    }

    public function delete()
    {
        $this->getManager()->delete($this);
    }

    public function isInDB()
    {
        $pk = $this->getManager()->getPrimaryKey();
        return $this->$pk > 0;
    }

}