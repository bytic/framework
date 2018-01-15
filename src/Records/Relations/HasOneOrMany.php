<?php

namespace Nip\Records\Relations;

use Nip\Database\Query\Select as Query;
use Nip\HelperBroker;
use Nip\Records\AbstractModels\Record as Record;
use Nip\Records\Collections\Associated as AssociatedCollection;
use Nip\Records\Collections\Collection;
use Nip\Records\Collections\Collection as RecordCollection;

/**
 * Class HasOneOrMany.
 */
abstract class HasOneOrMany extends Relation
{
    /**
     * @var string
     */
    protected $type = 'hasMany';

    /**
     * @return bool
     */
    public function save()
    {
        if ($this->hasResults()) {
            $collection = $this->getResults();
            foreach ($collection as $item) {
                $this->saveResult($item);
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function hasResults()
    {
        return $this->isPopulated() && count($this->getResults()) > 0;
    }

    /**
     * @param Record $item
     */
    public function saveResult(Record $item)
    {
        $pk = $this->getManager()->getPrimaryKey();
        $fk = $this->getFK();
        $item->{$fk} = $this->getItem()->{$pk};
        $item->saveRecord();
    }

    public function initResults()
    {
        $query = $this->getQuery();
        $items = $this->getWith()->findByQuery($query);
        $collection = $this->newCollection();
        $this->populateCollection($collection, $items);
        $this->setResults($collection);
    }

    /**
     * @return AssociatedCollection
     */
    public function newCollection()
    {
        $class = $this->getCollectionClass();
        $collection = new $class();
        /* @var AssociatedCollection $collection */
        $collection->initFromRelation($this);

        return $collection;
    }

    /**
     * @return mixed|string
     */
    public function getCollectionClass()
    {
        $collection = $this->getParam('collection');
        if ($collection) {
            return $collection;
        }

        return 'Nip\Records\Collections\Associated';
    }

    /**
     * @param RecordCollection $collection
     * @param $items
     */
    public function populateCollection(RecordCollection $collection, $items)
    {
        foreach ($items as $item) {
            $collection->add($item);
        }
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @param RecordCollection $collection
     *
     * @return Query
     */
    public function getEagerQuery(RecordCollection $collection)
    {
        $fkList = $this->getEagerFkList($collection);
        $query = $this->newQuery();
        $query->where($this->getFK().' IN ?', $fkList);

        return $query;
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @param RecordCollection $collection
     *
     * @return array
     */
    public function getEagerFkList(RecordCollection $collection)
    {
        $key = $collection->getManager()->getPrimaryKey();
        $return = HelperBroker::get('Arrays')->pluck($collection, $key);

        return array_unique($return);
    }

    /**
     * @param array      $dictionary
     * @param Collection $collection
     * @param Record     $record
     *
     * @return AssociatedCollection
     */
    public function getResultsFromCollectionDictionary($dictionary, $collection, $record)
    {
        $fk = $record->getManager()->getPrimaryKey();
        $pk = $record->{$fk};
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
     *
     * @return array
     */
    protected function buildDictionary(RecordCollection $collection)
    {
        $dictionary = [];
        $pk = $this->getDictionaryKey();
        foreach ($collection as $record) {
            $dictionary[$record->{$pk}][] = $record;
        }

        return $dictionary;
    }

    /**
     * @return string
     */
    protected function getDictionaryKey()
    {
        return $this->getFK();
    }
}
