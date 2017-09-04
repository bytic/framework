<?php

namespace Nip\Database\Query;

/**
 * Class Truncate
 * @package Nip\Database\Query
 */
class Truncate extends AbstractQuery
{

    /**
     * @return string
     */
    public function assemble()
    {
        return 'TRUNCATE TABLE ' . $this->getTable();
    }
}
