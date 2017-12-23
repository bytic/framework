<?php

namespace Nip\Records;

use Nip\Records\Traits\Relations\HasRelationsRecordTrait;

/**
 * Class Record
 * @package Nip\Records
 */
class Record extends AbstractModels\Record
{
    use HasRelationsRecordTrait;

    /**
     * Overloads Ucfirst() helper
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $return = $this->isCallRelationOperation($name, $arguments);
        if ($return !== false) {
            return $return;
        }

        return parent::__call($name, $arguments);
    }
}
