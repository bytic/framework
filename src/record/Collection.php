<?php

class Nip_RecordCollection extends Nip_Collection
{
    protected $_indexKey = false;

    public function __call($name, $arguments = array())
	{
		if (substr($name, 0, 3) == "get") {
			$class = substr($name, 3);
			$manager = $this->current()->getManager();
            list($type, $params) = $manager->hasRelation($class);
            if (!$type) {
                trigger_error('No relation defined for ' . $class, E_USER_WARNING);
                return;
            }

			return $this->_getRecords($class, $arguments[0]);
		}

		foreach ($this as $item) {
			call_user_func(array($item, $name), $arguments);
		}
	}

	public function __construct($items = array())
	{
		if ($items instanceof Record) {
			$pk = $items->getManager()->getPrimaryKey();
			$items = array($items->$pk => $items);
		}
		return parent::__construct($items);
	}

	protected function _getRecords($class, $populate)
	{
		if (is_null($populate)) {
			$populate = true;
		}

		$return = new Nip_RecordCollection_AssociatedAggregate();
		
		if (count($this)) {
			$manager = $this->current()->getManager();

            list($type, $params) = $manager->hasRelation($class);
            if ($type == 'belongsTo') {
                $manager = call_user_func(array($params['class'], "instance"));
                $fkList = Nip_Helper_Array::instance()->pluck($this, $params['fk']);

                $belongsTo = $manager->findByPrimary($fkList);
                foreach ($this as $item) {
                    $value = $belongsTo[$item->$params['fk']];
                    $item->setAssociated($class, $value ? $value : false);
                }
                foreach ($belongsTo as $item) {
                    $return->add($item);
                }
            } else {
                $return->setParams($params);
                $return->setModels($this);

                if ($populate) {
                    $return->populate();
                }
            }
        }

        return $return;
	}

	public function toJSON()
	{
		$return = array();
		foreach ($this as $item) {
			$return = $item->toArray();
		}

		return json_encode($return);
	}

	public function save()
	{
		if (count($this) > 0) {
			foreach ($this as $item) {
				$item->save();
			}
		}
	}

	public function add($record, $index = null)
    {
        if ($index) {
            $index = $record->$index;
        } else {
            $index = $this->getIndexKey();
            $index = $index ? $record->$index : $record->getPrimaryKey();
            if (!$index) {
                $index = null;
            }
        }

        return $this->offsetSet($index, $record);
    }

	public function getIndexKey()
    {
        return $this->_indexKey;
    }

	public function remove($record)
	{
		foreach ($this as $key => $item) {
			if ($item == $record) {
				unset($this[$key]);
			}
		}
	}

	/**
	 * When $each is true, each record gets it's delete() method called.
	 * Otherwise, a delete query is built for the entire collection
	 * 
	 * @param bool $each
	 * @return Nip_RecordCollection
	 */
	public function delete($each = false)
	{
		if (count($this) > 0) {
			if ($each) {
				foreach ($this as $item) {
					$item->delete();
				}
			} else {
				$manager = $this->rewind()->getManager();
				$pk = $manager->getPrimaryKey();
				$pk_list = Nip_Helper_Array::instance()->pluck($this, $pk);

				$query = $manager->newQuery("delete");
				$query->where("$pk IN ?", $pk_list);
				$query->execute();
			}

			$this->clear();
		}

		return $this;
	}

}