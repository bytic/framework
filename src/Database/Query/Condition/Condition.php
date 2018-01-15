<?php

namespace Nip\Database\Query\Condition;

use Nip\Database\Query\AbstractQuery as Query;

class Condition
{
    protected $_string;
    protected $_values;
    protected $_query;

    public function __construct($string, $values = [])
    {
        $this->_string = $string;
        $this->_values = $values;
    }

    public function __toString()
    {
        return $this->getString();
    }

    public function getString()
    {
        return $this->parseString($this->_string, $this->_values);
    }

    /**
     * Parses $string and replaces all instances of "?" with corresponding $values.
     *
     * @param string $string
     * @param array  $values
     *
     * @return string
     */
    public function parseString($string, $values)
    {
        $positions = [];
        $pos = 0;
        $offset = 0;

        while (($pos = strpos($string, '?', $offset)) !== false) {
            $positions[] = $pos;
            $offset = $pos + 1;
        }

        $count = count($positions);

        if ($count == 1) {
            $values = [$values];
        }

        for ($i = 0; $i < $count; $i++) {
            $value = $values[$i];
            if ($value instanceof Query) {
                $value = $this->parseValueQuery($value);
            } elseif (is_array($value)) {
                foreach ($value as $key => $subvalue) {
                    if (trim($subvalue) != '') {
                        $value[$key] = is_numeric($subvalue) ? $subvalue : $this->getQuery()->getManager()->getAdapter()->quote($subvalue);
                    } else {
                        unset($value[$key]);
                    }
                }
                $value = '('.implode(', ', $value).')';
            } elseif (is_numeric($value)) {
            } else {
                $value = $this->getQuery()->getManager()->getAdapter()->quote($values[$i]);
            }
            $string = substr_replace($string, $value, strpos($string, '?'), 1);
        }

        return $string;
    }

    protected function parseValueQuery($value)
    {
        return '('.$value->assemble().')';
    }

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->_query;
    }

    /**
     * @param Query $query
     *
     * @return $this
     */
    public function setQuery($query)
    {
        $this->_query = $query;

        return $this;
    }

    public function and_($condition)
    {
        return new AndCondition($this, $condition);
    }

    public function or_($condition)
    {
        return new OrCondition($this, $condition);
    }

    public function protectCondition($condition)
    {
        return strpos($condition, ' AND ') || strpos($condition, ' OR ') ? '('.$condition.')' : $condition;
    }
}
