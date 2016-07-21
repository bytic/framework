<?php

namespace Nip\Records\Collections;

use Nip_Record as Record;
use Nip\Records\Relations\HasOneOrMany as Relation;
use Nip_RecordCollection as RecordCollection;

class Associated extends RecordCollection
{

    /**
     * @var Relation
     */
    protected $_relation;

    /**
     * @var Record
     */
    protected $_item;

    /**
     * @return Relation
     */
    public function getRelation()
    {
        return $this->_relation;
    }

    /**
     * @param Relation $relation
     */
    public function setRelation($relation)
    {
        $this->_relation = $relation;
    }

    public function initFromRelation(Relation $relation)
    {
        $this->setRelation($relation);
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