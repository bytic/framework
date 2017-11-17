<?php

namespace Nip\Database\Query;

/**
 * Class Update
 * @package Nip\Database\Query
 */
class Update extends AbstractQuery
{

    /**
     * @return string
     */
    public function assemble()
    {
        $query = "UPDATE {$this->protect($this->getTable())} SET {$this->parseUpdate()}";

        $query .= $this->assembleWhere();
        $query .= $this->assembleLimit();

        return $query;
    }

    /**
     * @return bool|string
     */
    public function parseUpdate()
    {
        if (!$this->parts['data']) {
            return false;
        }
        $fields = [];
        foreach ($this->parts['data'] as $data) {
            foreach ($data as $key => $values) {
                if (!is_array($values)) {
                    $values = [$values];
                }
                $value = $values[0];
                $quote = isset($values[1]) ? $values[1] : null;

                if (!is_numeric($value)) {
                    if (is_null($quote)) {
                        $quote = true;
                    }
                    if ($quote) {
                        $value = $this->getManager()->getAdapter()->quote($value);
                    }
                }

                $fields[] = "{$this->protect($key)} = $value";
            }
        }

        return implode(", ", $fields);
    }
}
