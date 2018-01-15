<?php

namespace Nip\Database\Query\Condition;

class AndCondition extends Condition
{
    protected $_condition;
    protected $_andCondition;

    public function __construct($condition, $andCondition)
    {
        $this->_condition = $condition;
        $this->_andCondition = $andCondition;
    }

    public function getString()
    {
        return $this->protectCondition($this->_condition->getString()).' AND '.$this->protectCondition($this->_andCondition->getString()).'';
    }
}
