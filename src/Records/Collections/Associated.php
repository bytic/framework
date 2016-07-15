<?php

class Nip_RecordCollection_Associated extends Nip_RecordCollection
{


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

}