<?php

namespace Nip\Records\Relations;

use Nip\Database\Query\AbstractQuery;

/**
 * Class HasMany.
 */
class HasMany extends HasOneOrMany
{
    /**
     * @param AbstractQuery $query
     *
     * @return AbstractQuery
     */
    public function populateQuerySpecific(AbstractQuery $query)
    {
        $pk = $this->getManager()->getPrimaryKey();
        $query->where('`'.$this->getFK().'` = ?', $this->getItem()->{$pk});

        return $query;
    }
}
