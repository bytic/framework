<?php

namespace Nip\Records\Relations;

use Nip\Database\Query\Select as SelectQuery;
use Nip\Records\Collections\Associated as AssociatedCollection;
use Nip\Records\Collections\Collection;
use Nip\Records\Collections\Collection as RecordCollection;
use Nip\Records\Record;
use Nip\Records\Relations\Traits\HasCollectionResults;
use Nip\Records\Relations\Traits\HasPivotTable;

/**
 * Class HasOneOrMany
 * @package Nip\Records\Relations
 */
class MorphToMany extends Relation
{
    use HasCollectionResults;
    use HasPivotTable;

    /**
     * The type of the polymorphic relation.
     *
     * @var string
     */
    protected $morphType;

    /**
     * The class name of the morph type constraint.
     *
     * @var string
     */
    protected $morphClass;

    /**
     * Indicates if we are connecting the inverse of the relation.
     *
     * This primarily affects the morphClass constraint.
     *
     * @var bool
     */
    protected $inverse = false;

    /**
     * @return bool
     */
    public function isInverse(): bool
    {
        return $this->inverse;
    }

    /**
     * @param bool $inverse
     */
    public function setInverse(bool $inverse)
    {
        $this->inverse = $inverse;
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @return SelectQuery
     */
    public function newQuery()
    {
        $query = $this->getDB()->newSelect();

        $query->from($this->getWith()->getFullNameTable());
        $query->from($this->getDB()->getDatabase() . '.' . $this->getTable());

//        foreach ($this->getWith()->getFields() as $field) {
//            $query->cols(["{$this->getWith()->getTable()}.$field", $field]);
//        }

        $pk = $this->getWith()->getPrimaryKey();
        $fk = $this->getWith()->getPrimaryFK();
        $query->where("`{$this->getTable()}`.`$fk` = `{$this->getWith()->getTable()}`.`$pk`");

        $order = $this->getParam('order');
        if ($order) {
            foreach ($order as $item) {
                $query->order([$item[0], $item[1]]);
            }
        }

        return $query;
    }


    /**
     * @param array $dictionary
     * @param Collection $collection
     * @param Record $record
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

    public function initResults()
    {
//        $query = $this->getQuery();
//        $items = $this->getWith()->findByQuery($query);
        $collection = $this->newCollection();
//        $this->populateCollection($collection, $items);
        $this->setResults($collection);
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
//        $pk = $this->getDictionaryKey();
//        foreach ($collection as $record) {
//            $dictionary[$record->{$pk}][] = $record;
//        }
        return $dictionary;
    }
}