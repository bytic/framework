<?php

class Nip_Form_Element_Hash extends Nip_Form_Element_Hidden
{
    protected $_ID;

    public function init()
    {
        parent::init();
        $this->initSession();
    }

    public function initSession()
    {
        $name = $this->getSessionName();
        if (!$_SESSION[$name]) {
            $this->reset();
        }

        $this->setValue($this->getSessionValue());
    }

    public function getSessionName()
    {
        return $this->getForm()->getName() . '_' . $this->getSalt();
    }

    public function getSalt()
    {
        return sha1(__CLASS__);
    }

    public function reset()
    {
        $name = $this->getSessionName();
        $hash = $this->_generateHash();
        $_SESSION[$name] = $hash;
        $this->setValue($hash);
    }

    protected function _generateHash()
    {
        return md5(
            mt_rand(1, 1000000)
            . $this->getSalt()
            . $this->getName()
            . session_id()
            . mt_rand(1, 1000000)
        );
    }

    public function getSessionValue()
    {
        $name = $this->getSessionName();
        return $_SESSION[$name];
    }

    public function validate()
    {
        if (!$this->getValue()) {
            $this->addError('Request received without security hash');
        } elseif ($this->getValue() != $this->getSessionValue()) {
            $this->addError('Form security hash different from server');
        }
    }
}
