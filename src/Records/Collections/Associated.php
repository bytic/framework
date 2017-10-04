<?php

namespace Nip\Records\Collections;

use Nip\Records\Collections\Collection as RecordCollection;
use Nip\Records\Record as Record;
use Nip\Records\Relations\HasOneOrMany as Relation;
use Nip\Records\Relations\Traits\HasCollectionResults;

class Associated extends RecordCollection
{

    /**
     * @var Relation
     */
    protected $_withRelation;

    /**
     * @var Record
     */
    protected $_item;

    /**
     * @param HasCollectionResults $relation
     */
    public function initFromRelation($relation)
    {
        $this->setWithRelation($relation);
        $this->setManager($relation->getWith());
        $this->setItem($relation->getItem());
        $indexKey = $relation->getParam('indexKey');
        if ($indexKey) {
            $this->setIndexKey($indexKey);
        }
    }

    public function save()
    {
        return $this->getWithRelation()->save();
    }

    /**
     * @return Relation
     */
    public function getWithRelation()
    {
        return $this->_withRelation;
    }

    /**
     * @param Relation $relation
     */
    public function setWithRelation($relation)
    {
        $this->_withRelation = $relation;
    }

    /**
     * @return Record
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     * @param Record $item
     */
    public function setItem($item)
    {
        $this->_item = $item;
    }
}
