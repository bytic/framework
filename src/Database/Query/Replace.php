<?php

namespace Nip\Database\Query;

/**
 * Class Replace
 * @package Nip\Database\Query
 */
class Replace extends Insert
{

    /**
     * @return string
     */
    public function assemble()
    {
        $query = "REPLACE INTO " . $this->protect($this->getTable()) . $this->parseCols() . $this->parseValues();

        return $query;
    }
}
