<?php

class Nip_DB_Query_SelectUnion extends Nip_DB_Query_Select
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
		$query = ($this->_query1 instanceof Nip_DB_Query_SelectUnion) ? "(".$this->_query1.")" : $this->_query1;
		$query .= " UNION ";
		$query .= ($this->_query2 instanceof Nip_DB_Query_SelectUnion) ? "(".$this->_query2.")" : $this->_query2;

		$order = $this->parseOrder();

		if (!empty($order)) {
			$query .= " ORDER BY $order";
		}

		if (!empty($this->_parts['limit'])) {
			$query .= " LIMIT {$this->_parts['limit']}";
		}

		return $query;
	}

}