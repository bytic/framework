<?php
class Nip_RecordCollection_AssociatedAggregate extends Nip_RecordCollection_Associated
{
    /**
     * @var Nip_RecordCollection
     */
    protected $_models;

	public function __call($name, $arguments = array())
	{
		foreach ($this as $item) {
			call_user_func_array(array($item, $name), $arguments);
		}
	}

    /**
     * @return Nip_DB_Query_Select
     */
    public function getQuery()
    {
		if (count($this->getWith())) {
			$method = "get" . $this->getParam("name");

			$query = $this->getItem()->$method(false)->getQuery(false);
            $query->where("`{$this->getParam("table")}`.`{$this->getParam("fk")}` IN ?", pluck($this->getModels(), $this->getManager()->getPrimaryKey()));
                
            return $query;
		}
        
		return $return;
    }

    public function populate() 
    {
        $items = $this->getModels();
        $method = "get{$this->getParam("name")}";

        $results = $this->getQuery()->execute();
        if ($results->numRows()) {

            $key = $this->getParam("fk");
            if ($this->getManager()->hasAndBelongsToMany($this->getWith())) {
                $key = "__$key";
            }

            while ($row = $results->fetchResult()) {
                $item = $this->getWith()->getNew($row);
                $items[$item->$key]->$method(false)->add($item);
            }
        }

        $pk = $this->getManager()->getPrimaryKey();
        foreach ($items as $item) {
            $this[$item->$pk] = $item->$method(false);
        }
    }

    public function flatten()
    {
        $return = new Nip_RecordCollection();
        if (count($this)) {
            foreach ($this as $collection) {
                if (count($collection)) {
                    $pk = $collection->getWith()->getPrimaryKey();
                    foreach ($collection as $item) {
                        $return[$pk] = $item;
                    }
                }
            }
        }
        return $return;
    }

    public function getModels()
    {
        return $this->_models;
    }

    public function setModels($models)
    {
        $this->_models = $models;
        return $this;
    }

    public function getItem()
    {
        return $this->getModels()->rewind();
    }

}