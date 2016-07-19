<?php

namespace Nip\Records\Relations;

use Nip_Record as Record;
use Nip_RecordCollection as RecordCollection;

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

    public function saveResult(Record $item)
    {
        $pk = $this->getManager()->getPrimaryKey();
        $fk = $this->getFK();
        $item->$fk = $this->getItem()->$pk;
        $item->saveRecord();
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
        /** @var RecordCollection $collection */
        $collection->setManager($this->getWith());
        return $collection;
    }

    public function getCollectionClass()
    {
        return 'Nip_RecordCollection';
    }

    function getResultsFromCollectionDictionary($dictionary, $collection, $record)
    {
        $pk = $record->{$this->getFK()};
        $collection = $this->newCollection();
        if ($dictionary[$pk]) {
            foreach ($dictionary[$pk] as $record) {
                $collection->add($record);
            }
        }

        return $collection;
    }

    /**
     * Build model dictionary keyed by the relation's foreign key.
     *
     * @param RecordCollection $collection
     * @return array
     */
    protected function buildDictionary(RecordCollection $collection)
    {
        $dictionary = [];
        $pk = $this->getFK();
        foreach ($collection as $record) {
            $dictionary[$record->{$pk}][] = $record;
        }
        return $dictionary;
    }
}