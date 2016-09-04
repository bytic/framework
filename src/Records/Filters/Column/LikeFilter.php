<?php

namespace Nip\Records\Filters\Column;

use Nip\Database\Query\Select as SelectQuery;

class LikeFilter extends AbstractFilter implements FilterInterface
{

    /**
     * @param SelectQuery $query
     */
    public function filterQuery($query)
    {
        $query->where("{$this->getDbName()} LIKE ?", '%'.$this->getValue().'%');
    }
}