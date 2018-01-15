<?php

namespace Nip\Database\Query;

use Nip\Database\Query\Select\Union;

/**
 * Class Select.
 *
 * @method $this options() options(string $option = null)
 * @method $this setFrom() setFrom(string $table = null)
 * @method $this setOrder() setOrder(array|string $cols = null)
 */
class Select extends AbstractQuery
{
    /**
     * @param $name
     * @param $arguments
     *
     * @return $this
     */
    public function __call($name, $arguments)
    {
        if (in_array($name, ['min', 'max', 'count', 'avg', 'sum'])) {
            $input = reset($arguments);

            if (is_array($input)) {
                $input[] = false;
            } else {
                $alias = isset($arguments[1]) ? $arguments[1] : null;
                $protected = isset($arguments[2]) ? $arguments[2] : null;
                $input = [$input, $alias, $protected];
            }

            $input[0] = strtoupper($name).'('.$this->protect($input[0]).')';

            return $this->cols($input);
        }

        return parent::__call($name, $arguments);
    }

    /**
     * Inserts FULLTEXT statement into $this->select and $this->where.
     *
     * @param mixed  $fields
     * @param string $against
     * @param string $alias
     * @param bool   $boolean_mode
     *
     * @return $this
     */
    public function match($fields, $against, $alias, $boolean_mode = true)
    {
        if (!is_array($fields)) {
            $fields = [];
        }

        $match = [];
        foreach ($fields as $itemField) {
            if (!is_array($itemField)) {
                $itemField = [$itemField];

                $field = isset($itemField[0]) ? $itemField[0] : false;
                $protected = isset($itemField[1]) ? $itemField[1] : true;

                $match[] = $protected ? $this->protect($field) : $field;
            }
        }
        $match = 'MATCH('.implode(',',
                $match).") AGAINST ('".$against."'".($boolean_mode ? ' IN BOOLEAN MODE' : '').')';

        return $this->cols([$match, $alias, false])->where([$match]);
    }

    /**
     * Inserts JOIN entry for the last table inserted by $this->from().
     *
     * @param mixed       $table the table to be joined, given as simple string or name - alias pair
     * @param string|bool $on
     * @param string      $type  SQL join type (INNER, OUTER, LEFT INNER, etc.)
     *
     * @return $this
     */
    public function join($table, $on = false, $type = '')
    {
        $lastTable = end($this->parts['from']);

        if (!$lastTable) {
            trigger_error('No previous table to JOIN', E_USER_ERROR);
        }

        if (is_array($lastTable)) {
            $lastTable = $lastTable[1];
        }

        $this->parts['join'][$lastTable][] = [$table, $on, $type];

        return $this;
    }

    /**
     * Sets the group paramater for the query.
     *
     * @param array $fields
     * @param bool  $rollup suport for modifier WITH ROLLUP
     *
     * @return $this
     */
    public function group($fields, $rollup = false)
    {
        $this->parts['group']['fields'] = $fields;
        $this->parts['group']['rollup'] = $rollup;

        return $this;
    }

    /**
     * @return string
     */
    public function assemble()
    {
        $select = $this->parseCols();
        $options = $this->parseOptions();
        $from = $this->parseFrom();

        $group = $this->parseGroup();
        $having = $this->parseHaving();

        $order = $this->parseOrder();

        $query = 'SELECT';

        if (!empty($options)) {
            $query .= " $options";
        }

        if (!empty($select)) {
            $query .= " $select";
        }

        if (!empty($from)) {
            $query .= " FROM $from";
        }

        $query .= $this->assembleWhere();

        if (!empty($group)) {
            $query .= " GROUP BY $group";
        }

        if (!empty($having)) {
            $query .= " HAVING $having";
        }

        if (!empty($order)) {
            $query .= " ORDER BY $order";
        }

        $query .= $this->assembleLimit();

        return $query;
    }

    /**
     * @return null|string
     */
    public function parseOptions()
    {
        if (!empty($this->parts['options'])) {
            return implode(' ', array_map('strtoupper', $this->parts['options']));
        }
    }

    /**
     * @param $query
     *
     * @return Union
     */
    public function union($query)
    {
        return new Union($this, $query);
    }

    /**
     * Parses SELECT entries.
     *
     * @return string
     */
    protected function parseCols()
    {
        if (!isset($this->parts['cols']) or !is_array($this->parts['cols']) or count($this->parts['cols']) < 1) {
            return '*';
        } else {
            $selectParts = [];

            foreach ($this->parts['cols'] as $itemSelect) {
                if (is_array($itemSelect)) {
                    $field = isset($itemSelect[0]) ? $itemSelect[0] : false;
                    $alias = isset($itemSelect[1]) ? $itemSelect[1] : false;
                    $protected = isset($itemSelect[2]) ? $itemSelect[2] : true;

                    $selectParts[] = ($protected ? $this->protect($field) : $field).(!empty($alias) ? ' AS '.$this->protect($alias) : '');
                } else {
                    $selectParts[] = $itemSelect;
                }
            }

            return implode(', ', $selectParts);
        }
    }

    /**
     * Parses FROM entries.
     *
     * @return string
     */
    private function parseFrom()
    {
        if (!empty($this->parts['from'])) {
            $parts = [];

            foreach ($this->parts['from'] as $key => $item) {
                if (is_array($item)) {
                    $table = isset($item[0]) ? $item[0] : false;
                    $alias = isset($item[1]) ? $item[1] : false;

                    if (is_object($table)) {
                        if (!$alias) {
                            trigger_error('Select statements in for need aliases defined', E_USER_ERROR);
                        }
                        $parts[$key] = '('.$table.') AS '.$this->protect($alias).$this->parseJoin($alias);
                    } else {
                        $parts[$key] = $this->protect($table).' AS '.$this->protect((!empty($alias) ? $alias : $table)).$this->parseJoin($alias);
                    }
                } elseif (!strpos($item, ' ')) {
                    $parts[] = $this->protect($item).$this->parseJoin($item);
                } else {
                    $parts[] = $item;
                }
            }

            return implode(', ', array_unique($parts));
        }
    }

    /**
     * Parses JOIN entries for a given table
     * Concatenates $this->join entries for input table.
     *
     * @param string $table table to build JOIN statement for
     *
     * @return string
     */
    private function parseJoin($table)
    {
        $result = '';

        if (isset($this->parts['join'][$table])) {
            foreach ($this->parts['join'][$table] as $join) {
                if (!is_array($join[0])) {
                    $join[0] = [$join[0]];
                }

                $joinTable = isset($join[0][0]) ? $join[0][0] : false;
                $joinAlias = isset($join[0][1]) ? $join[0][1] : false;
                $joinOn = isset($join[1]) ? $join[1] : false;

                $joinType = isset($join[2]) ? $join[2] : '';

                $result .= ($joinType ? ' '.strtoupper($joinType) : '').' JOIN ';
                if (strpos($joinTable, '(') !== false) {
                    $result .= $joinTable;
                } else {
                    $result .= $this->protect($joinTable);
                }
                $result .= (!empty($joinAlias) ? ' AS '.$this->protect($joinAlias) : '');

                if ($joinOn) {
                    $result .= ' ON ';
                    if (is_array($joinOn)) {
                        $result .= $this->protect($table.'.'.$joinOn[0]).' = '.$this->protect($joinTable.'.'.$joinOn[1]);
                    } else {
                        $result .= '('.$joinOn.')';
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Parses GROUP entries.
     *
     * @uses $this->group['fields'] array with elements to group by
     *
     * @return string
     */
    private function parseGroup()
    {
        $group = '';
        if (isset($this->parts['group']['fields'])) {
            if (is_array($this->parts['group']['fields'])) {
                $groupFields = [];
                foreach ($this->parts['group']['fields'] as $field) {
                    $field = is_array($field) ? $field : [$field];
                    $column = isset($field[0]) ? $field[0] : false;
                    $type = isset($field[1]) ? $field[1] : '';

                    $groupFields[] = $this->protect($column).($type ? ' '.strtoupper($type) : '');
                }

                $group .= implode(', ', $groupFields);
            } else {
                $group .= $this->parts['group']['fields'];
            }
        }

        if (isset($this->parts['group']['rollup']) && $this->parts['group']['rollup'] !== false) {
            $group .= ' WITH ROLLUP';
        }

        return $group;
    }
}
