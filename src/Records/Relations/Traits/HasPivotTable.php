<?php

namespace Nip\Records\Relations\Traits;

use Nip\Database\Connections\Connection;

/**
 * Trait HasPivotTable
 * @package Nip\Records\Relations\Traits
 */
trait HasPivotTable
{

    /**
     * @return Connection
     */
    public function getDB()
    {
        return $this->getParam("link-db") == 'with' ? $this->getWith()->getDB() : parent::getDB();
    }


    /** @noinspection PhpMissingParentCallCommonInspection
     * @return string
     */
    public function generateTable()
    {
        return $this->getPivotTable();
    }

    /**
     * Builds the name of a has-and-belongs-to-many association table
     * @return string
     */
    public function getPivotTable()
    {
        $tables = [
            $this->getManager()->getTable(),
            $this->getWith()->getTable()
        ];
        sort($tables);

        return implode("-", $tables);
    }
}