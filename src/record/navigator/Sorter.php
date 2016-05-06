<?php

class Nip_Record_Sorter
{
	
	protected $_query;
	protected $_field;
	protected $_type = "asc";

	public function  __construct($field = false, $type = false)
	{
		$this->_field = $field;

		if ($type) {
			$this->_type = $type;
		}
	}

	public function sort($query, $request = array())
	{
		$this->setQuery($query);
		$this->setParams($request);

		if ($this->getField()) {
			$query->order(array($this->getField(), $this->getType()));
		}
		return $query;
	}

	public function setParams($request = array()) 
	{
		if ($request['order'] && preg_match("/[a-z0-9_-]/i", $request['order'])) {
			$this->_field = $request['order'];
			if (in_array($request['order_type'], array("asc", "desc"))) {
				$this->_type = $request['order_type'];
			}
		}
		return $this;
	}

	/**
	 * @param Nip_DB_Query_Select $query
	 * @return Nip_Record_Sorter
	 */
	public function setQuery($query)
	{
		$this->_query = $query;
		return $this;
	}

	/**
	 * @return Nip_DB_Query_Select
	 */
	public function getQuery()
	{
		return $this->_query;
	}

	public function getField()
	{
		return $this->_field;
	}

	public function getType()
	{
		return $this->_type;
	}

}
