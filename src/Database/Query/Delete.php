<?php

namespace Nip\Database\Query;

/**
 * Class Delete.
 */
class Delete extends AbstractQuery
{
    /**
     * Joins together DELETE, FROM, WHERE, ORDER, and LIMIT parts of SQL query.
     *
     * @return string
     */
    public function assemble()
    {
        $order = $this->parseOrder();

        $query = 'DELETE FROM '.$this->getManager()->protect($this->getTable());

        $query .= $this->assembleWhere();

        if (!empty($order)) {
            $query .= ' ORDER BY '.$order;
        }

        $query .= $this->assembleLimit();

        return $query;
    }
}
