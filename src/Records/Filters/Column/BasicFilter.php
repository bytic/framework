<?php

namespace Nip\Records\Filters\Column;

use Nip\Database\Query\Select as SelectQuery;

/**
 * Class BasicFilter.
 */
class BasicFilter extends AbstractFilter implements FilterInterface
{
    /**
     * @var string
     */
    protected $databaseOperation = '=';

    /**
     * @param SelectQuery $query
     */
    public function filterQuery($query)
    {
        $query->where("{$this->getDbName()} = ?", $this->getValue());
    }

    /**
     * @return string
     */
    public function getDatabaseOperation()
    {
        return $this->databaseOperation;
    }

    /**
     * @param string $databaseOperation
     */
    public function setDatabaseOperation($databaseOperation)
    {
        $this->databaseOperation = $databaseOperation;
    }
}
