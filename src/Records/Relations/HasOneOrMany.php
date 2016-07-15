<?php

namespace Nip\Records\Relations;

abstract class HasOneOrMany extends Relation
{

    public function save()
    {
        if ($this->hasResults()) {
            $collection = $this->getResults();
            foreach ($collection as $item) {
                $this->saveResult($item);
            }
        }
    }

    public function saveResult($item)
    {
        $pk = $this->getManager()->getPrimaryKey();
        $fk = $this->getFK();
        $item->$fk = $this->getItem()->$pk;
        $item->saveRecords();
    }

    public function hasResults()
    {
        return $this->isPopulated() && count($this->getResults()) > 0;
    }

    public function initResults()
    {
        $query = $this->getQuery();
        $items = $this->getWith()->findByQuery($query);
        $collection = $this->newCollection();
        $this->populateCollection($collection, $items);
        $this->setResults($collection);
    }

    public function populateCollection($collection, $items)
    {
        foreach ($items as $item) {
            $collection->add($item);
        }
    }

    public function newCollection()
    {
        $class = $this->getCollectionClass();
        $collection = new $class();
        return $collection;
    }

    public function getCollectionClass()
    {
        return 'Nip_RecordCollection_Associated';
    }
}