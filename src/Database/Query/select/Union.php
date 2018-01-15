<?php

namespace Nip\Database\Query\Select;

use Nip\Database\Query\Select;

class Union extends Select
{
    protected $_query1;
    protected $_query2;

    public function __construct($query1, $query2)
    {
        $this->_query1 = $query1;
        $this->_query2 = $query2;
    }

    public function assemble()
    {
        $query = ($this->_query1 instanceof self) ? '('.$this->_query1.')' : $this->_query1;
        $query .= ' UNION ';
        $query .= ($this->_query2 instanceof self) ? '('.$this->_query2.')' : $this->_query2;

        $order = $this->parseOrder();

        if (!empty($order)) {
            $query .= " ORDER BY $order";
        }

        if (!empty($this->parts['limit'])) {
            $query .= " LIMIT {$this->parts['limit']}";
        }

        return $query;
    }
}
