<?php

use Nip\Database\Query\_Abstract;

class Nip_DB_Query_Update extends _Abstract
{

    public function assemble()
    {
        $query = "UPDATE " . $this->protect($this->getTable()) . " SET " . $this->parseUpdate() .
            ($this->_parts['where'] ? ' WHERE ' . $this->parseWhere() : '') .
            ($this->_parts['limit'] ? ' LIMIT ' . $this->limit : '');
        return $query;
    }

    public function parseUpdate()
    {
        if (!$this->_parts['data']) {
            return false;
        }

        foreach ($this->_parts['data'] as $data) {
            foreach ($data as $key => $value) {
                if (!is_array($value)) {
                    $value = array($value);
                }
                list($value, $quote) = $value;

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