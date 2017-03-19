<?php

namespace Nip\Records\Filters\Column;

use Nip\Database\Query\Select as SelectQuery;

/**
 * Class BasicFilter
 * @package Nip\Records\Filters\Column
 */
class WildcardFilter extends BasicFilter implements FilterInterface
{

    /**
     * @var string
     */
    protected $databaseOperation = 'LIKE%%';

    /**
     * @param SelectQuery $query
     */
    public function filterQuery($query)
    {
        $query->where("{$this->getDbName()} = ?", $this->getValue());
    }
}
