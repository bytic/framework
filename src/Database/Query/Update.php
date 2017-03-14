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
        $query = "UPDATE ".$this->protect($this->getTable())." SET ".$this->parseUpdate();

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
            foreach ($data as $key => $value) {
                if (!is_array($value)) {
                    $value = [$value];
                }
                $value = $value[0];
                $quote = isset($value[1]) ? $value[1] : null;

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
