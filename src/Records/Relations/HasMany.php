<?php

namespace Nip\Records\Relations;

use Nip\Database\Query\AbstractQuery;
use Nip\Database\Query\Select as Query;

/**
 * Class HasMany
 * @package Nip\Records\Relations
 */
class HasMany extends HasOneOrMany
{

    /**
     * @param AbstractQuery $query
     * @return AbstractQuery
     */
    public function populateQuerySpecific(AbstractQuery $query)
    {
        $pk = $this->getManager()->getPrimaryKey();
        $query->where('`' . $this->getFK() . '` = ?', $this->getItem()->{$pk});

        return $query;
    }
}
