<?php

namespace Nip\Records\Filters\Column;

use Nip\Database\Query\Select as SelectQuery;

class InFilter extends AbstractFilter implements FilterInterface
{

    /**
     * @param SelectQuery $query
     */
    public function filterQuery($query)
    {
        $query->where("{$this->getDbName()} IN ?", $this->getValue());
    }

}