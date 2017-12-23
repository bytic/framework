<?php

namespace Nip\Database\Query;

/**
 * Class Delete
 * @package Nip\Database\Query
 */
class Delete extends AbstractQuery
{

    /**
     * Joins together DELETE, FROM, WHERE, ORDER, and LIMIT parts of SQL query
     * @return string
     */
    public function assemble()
    {
        $query = "DELETE FROM {$this->getManager()->protect($this->getTable())}";

        $query .= $this->assembleWhere();

        $order = $this->parseOrder();
        if (!empty($order)) {
            $query .= " order by {$order}";
        }

        $query .= $this->assembleLimit();

        return $query;
    }
}
