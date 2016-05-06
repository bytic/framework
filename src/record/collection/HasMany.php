<?php
class Nip_RecordCollection_HasMany extends Nip_RecordCollection_Associated {

	public function getQuery($specific = true)
	{
		if (!$this->_query) {
			$query = $this->getWith()->paramsToQuery();

			if ($specific) {
				$this->populateQuerySpecific($query);
			}

			$this->_query = $query;
		}

		return $this->_query;
	}

	public function populateQuerySpecific($query)
	{
		$pk = $this->getManager()->getPrimaryKey();
		$query->where('`' . $this->getParam("fk").'` = ?', $this->getItem()->$pk);
		return $query;
	}

	public function save()
	{
		if (count($this)) {
			$pk = $this->getManager()->getPrimaryKey();
			$fk = $this->getParam("fk");

			foreach ($this as $item) {
				$item->$fk = $this->getItem()->$pk;
			}
			parent::save();
		}
	}

	public function remove($record)
	{
		parent::remove($record);
		$record->delete();
		return $this;
	}

}