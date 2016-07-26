<?php

namespace Nip\Records\Collections;

use Nip\Records\Record as Record;
use Nip\Records\Relations\HasOneOrMany as Relation;
use Nip_RecordCollection as RecordCollection;

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

    public function initFromRelation(Relation $relation)
    {
        $this->setWithRelation($relation);
        $this->setManager($relation->getWith());
        $this->setItem($relation->getItem());
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