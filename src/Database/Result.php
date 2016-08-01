<?php

namespace Nip\Database;

class Result
{

	protected $_data;
	protected $_adapter;
	protected $_results = array();

	public function __construct($data, $adapter)
	{
		$this->_data = $data;
		$this->_adapter = $adapter;
	}

	public function __destruct()
	{
		if ($this->_data && !is_bool($this->_data)) {
			$this->_adapter->freeResults($this->_data);
		}
	}

	/**
	 * Fetches row from current result set
	 * @return array
	 */
	public function fetchResult()
	{
		try {
			return $this->_adapter->fetchAssoc($this->_data);
		} catch (Exception $e) {
			$e->log();
		}
	}

	/**
	 * Fetches all rows from current result set
	 * @return array
	 */
	public function fetchResults()
	{
		if (count($this->_results) == 0) {
			while ($result = $this->fetchResult()) {
				$this->_results[] = $result;
			}
		}
		return $this->_results;
	}

	public function numRows()
	{
		return $this->_adapter->numRows($this->_data);
	}

}