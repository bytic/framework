<?php

namespace Nip\Records\Relations;

use Nip\Database\Query\Select as Query;

class HasMany extends HasOneOrMany
{

    /**
     * @param Query $query
     * @return Query
     */
    public function populateQuerySpecific(Query $query)
    {
        $pk = $this->getManager()->getPrimaryKey();
        $query->where('`' . $this->getFK() . '` = ?', $this->getItem()->$pk);
        return $query;
    }

}