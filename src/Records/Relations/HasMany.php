<?php

namespace Nip\Records\Relations;

class HasMany extends Relation
{

    public function populateQuerySpecific($query)
    {
        $pk = $this->getManager()->getPrimaryKey();
        $query->where('`' . $this->getParam("fk").'` = ?', $this->getItem()->$pk);
        return $query;
    }
}