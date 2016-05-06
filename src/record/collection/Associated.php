<?php

class Nip_RecordCollection_Associated extends Nip_RecordCollection
{
	protected $_item;
	protected $_manager;
	protected $_with;
	protected $_params = array();
	protected $_query;
	protected $_populated = false;

	public function populate()
	{
        if (!$this->_populated && !count($this->_items)) {
            $this->_items = array();
            $query = $this->getQuery();
            $items = $this->getWith()->findByQuery($query);
            foreach ($items as $item) {
                $this->add($item);
            }
            $this->_populated = true;
        }
        return $this;
	}

//	public function add($record)
//	{
//		$pk = $this->getWith()->getPrimaryKey();
//		if ($record->$pk) {
//			if (!$this->exists($record->$pk)) {
//				$this[$record->$pk] = $record;
//			}
//		} else {
//			$this[] = $record;
//		}
//		return $this;
//	}

	public function remove($record)
	{
		$pk = $this->getWith()->getPrimaryKey();
		unset($this[$record->$pk]);
		return $this;
	}

	public function exists($index)
	{
        if (is_object($index)) {
    		$pk = $this->getWith()->getPrimaryKey();
            $index = $index->$pk;
        }
		return parent::exists($index);
	}

	public function get($index)
	{
        if (is_object($index)) {
    		$pk = $this->getWith()->getPrimaryKey();
            $index = $index->$pk;
        }

        return $this[$index];
	}


	public function setItem(Record $item)
	{
		$this->_item = $item;
		return $this;
	}

	/**
	 * @return Record
	 */
	public function getItem()
	{
		return $this->_item;
	}

	/**
	 * @return Records
	 */
	public function getManager()
	{
		return $this->getItem()->getManager();
	}

	/**
	 * @return Records
	 */
	public function getWith()
	{
		if (!$this->_with) {
			$this->_with = call_user_func(array($this->getParam("class"), "instance"));
		}
		return $this->_with;
	}

	public function getParam($key)
	{
		return $this->_params[$key];
	}

	public function setParams($params = array())
	{
		$this->_params = $params;
        if ($this->_params['indexKey']) {
            $this->_indexKey = $this->_params['indexKey'];
        }
		return $this;
	}

	public function newQuery()
	{		
		return $this->getWith()->paramsToQuery();
	}

	public function getDB()
	{		
		return $this->getManager()->getDB();
	}

	public function setQuery($query)
	{
		$this->_query = $query;
		return $this;
	}
}