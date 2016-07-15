<?php
class Nip_DB_Query_OrCondition extends Nip_DB_Query_Condition {

    protected $_condition;
    protected $_orCondition;

    public function __construct($condition, $orCondition) {
        $this->_condition   = $condition;
        $this->_orCondition = $orCondition;
    }

    public function getString() {
        return $this->protectCondition($this->_condition->getString()) ." OR ". $this->protectCondition($this->_orCondition->getString()) ."";
    }
}