<?php

namespace Nip\Database\Query;

/**
 * Class Truncate.
 */
class Truncate extends AbstractQuery
{
    /**
     * @return string
     */
    public function assemble()
    {
        return 'TRUNCATE TABLE '.$this->getTable();
    }
}
