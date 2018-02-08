<?php

namespace Nip\Database\Query;

use Nip\Database\Connection;
use Nip\Database\Query\Condition\Condition;
use Nip\Database\Result;

/**
 * Class AbstractQuery.
 *
 * @method $this setCols() setCols(array | string $cols = null)
 * @method $this setWhere() setWhere(array | string $cols = null)
 * @method $this cols() cols(array | string $cols)
 * @method $this count() count(string $col, string $alias = null)
 * @method $this sum() sum(array | string $cols)
 * @method $this from() from(array | string $from)
 * @method $this data() data(array $data)
 * @method $this table() table(array | string $table)
 * @method $this order() order(array | string $order)\
 * @method $this group() group(array | string $group)\
 */
abstract class AbstractQuery
{
    /**
     * @var Connection
     */
    protected $db;

    protected $parts = [
        'where' => null,
    ];

    protected $string = null;

    /**
     * @param Connection $manager
     *
     * @return $this
     */
    public function setManager(Connection $manager)
    {
        $this->db = $manager;

        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return $this
     */
    public function __call($name, $arguments)
    {
        if (strpos($name, 'set') === 0) {
            $name = str_replace('set', '', $name);
            $name[0] = strtolower($name[0]);
            $this->initPart($name);
        }

        foreach ($arguments as $argument) {
            $this->addPart($name, $argument);
        }

        return $this;
    }

    /**
     * @param null $generated
     *
     * @return bool
     */
    public function isGenerated($generated = null)
    {
        if ($generated === false) {
            $this->string = null;
        }

        return $this->string !== null;
    }

    /**
     * @param $params
     */
    public function addParams($params)
    {
        $this->checkParamSelect($params);
        $this->checkParamFrom($params);
        $this->checkParamWhere($params);
        $this->checkParamOrder($params);
        $this->checkParamGroup($params);
        $this->checkParamHaving($params);
        $this->checkParamLimit($params);
    }

    /**
     * @param $string
     * @param array $values
     *
     * @return $this
     */
    public function where($string, $values = [])
    {
        /* @var Condition $this ->_parts[] */
        if ($string) {
            if (isset($this->parts['where']) && $this->parts['where'] instanceof Condition) {
                $this->parts['where'] = $this->parts['where']->and_($this->getCondition($string, $values));
            } else {
                $this->parts['where'] = $this->getCondition($string, $values);
            }
        }

        return $this;
    }

    /**
     * @param string $string
     * @param array $values
     *
     * @return Condition
     */
    public function getCondition($string, $values = [])
    {
        if (!is_object($string)) {
            $condition = new Condition($string, $values);
            $condition->setQuery($this);
        } else {
            $condition = $string;
        }

        return $condition;
    }

    /**
     * @param $start
     * @param bool $offset
     *
     * @return $this
     */
    public function limit($start, $offset = false)
    {
        $this->parts['limit'] = $start;
        if ($offset) {
            $this->parts['limit'] .= ','.$offset;
        }

        return $this;
    }

    /**
     * @param $string
     * @param array $values
     *
     * @return $this
     */
    public function orWhere($string, $values = [])
    {
        if ($string) {
            if ($this->parts['where'] instanceof Condition) {
                $this->parts['where'] = $this->parts['where']->or_($this->getCondition($string, $values));
            } else {
                $this->parts['where'] = $this->getCondition($string, $values);
            }
        }

        return $this;
    }

    /**
     * @param $string
     * @param array $values
     *
     * @return $this
     */
    public function having($string, $values = [])
    {
        if ($string) {
            if ($this->parts['having'] instanceof Condition) {
                $this->parts['having'] = $this->parts['having']->and_($this->getCondition($string, $values));
            } else {
                $this->parts['having'] = $this->getCondition($string, $values);
            }
        }

        return $this;
    }

    /**
     * Escapes data for safe use in SQL queries.
     *
     * @param string $data
     *
     * @return string
     */
    public function cleanData($data)
    {
        return $this->getManager()->getAdapter()->cleanData($data);
    }

    /**
     * @return Connection
     */
    public function getManager()
    {
        return $this->db;
    }

    /**
     * @return Result
     */
    public function execute()
    {
        return $this->getManager()->execute($this);
    }

    /**
     * Implements magic method.
     *
     * @return string This object as a Query string.
     */
    public function __toString()
    {
        return $this->getString();
    }

    /**
     * @return string
     */
    public function getString()
    {
        if ($this->string === null) {
            $this->string = (string)$this->assemble();
        }

        return $this->string;
    }

    /**
     * @return null
     */
    abstract public function assemble();

    /**
     * @return array
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * @param $name
     *
     * @return mixed|null
     */
    public function getPart($name)
    {
        return $this->hasPart($name) ? $this->parts[$name] : null;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasPart($name)
    {
        return isset($this->parts[$name]) && is_array($this->parts[$name]) && count($this->parts[$name]);
    }

    /**
     * @param $name
     *
     * @return $this
     */
    protected function initPart($name)
    {
        $this->isGenerated(false);
        $this->parts[$name] = [];

        return $this;
    }

    /**
     * @param $name
     * @param $value
     *
     * @return $this
     */
    protected function addPart($name, $value)
    {
        if (!isset($this->parts[$name])) {
            $this->initPart($name);
        }

        $this->isGenerated(false);
        $this->parts[$name][] = $value;

        return $this;
    }

    /**
     * @param $params
     */
    protected function checkParamSelect($params)
    {
        if (isset($params['select']) && is_array($params['select'])) {
            call_user_func_array([$this, 'cols'], $params['select']);
        }
    }

    /**
     * @param $params
     */
    protected function checkParamFrom($params)
    {
        if (isset($params['from']) && !empty($params['from'])) {
            $this->from($params['from']);
        }
    }

    /**
     * @param $params
     */
    protected function checkParamWhere($params)
    {
        if (isset($params['where']) && is_array($params['where'])) {
            foreach ($params['where'] as $condition) {
                $condition = (array)$condition;
                $this->where($condition[0], $condition[1]);
            }
        }
    }

    /**
     * @param $params
     */
    protected function checkParamOrder($params)
    {
        if (isset($params['order']) && !empty($params['order'])) {
            call_user_func_array([$this, 'order'], $params['order']);
        }
    }

    /**
     * @param $params
     */
    protected function checkParamGroup($params)
    {
        if (isset($params['group']) && !empty($params['group'])) {
            call_user_func_array([$this, 'group'], [$params['group']]);
        }
    }

    /**
     * @param $params
     */
    protected function checkParamHaving($params)
    {
        if (isset($params['having']) && !empty($params['having'])) {
            call_user_func_array([$this, 'having'], [$params['having']]);
        }
    }

    /**
     * @param $params
     */
    protected function checkParamLimit($params)
    {
        if (isset($params['limit']) && !empty($params['limit'])) {
            call_user_func_array([$this, 'limit'], [$params['limit']]);
        }
    }

    /**
     * @return null|string
     */
    protected function assembleWhere()
    {
        $where = $this->parseWhere();

        if (!empty($where)) {
            return " WHERE $where";
        }
    }

    /**
     * @return string
     */
    protected function parseWhere()
    {
        return is_object($this->parts['where']) ? (string)$this->parts['where'] : '';
    }

    /**
     * @return null|string
     */
    protected function assembleLimit()
    {
        $limit = $this->getPart('limit');
        if (!empty($limit)) {
            return " LIMIT {$this->parts['limit']}";
        }
    }

    /**
     * @param $name
     * @param $value
     *
     * @return $this
     */
    protected function setPart($name, $value)
    {
        $this->initPart($name);
        $this->addPart($name, $value);

        return $this;
    }

    /**
     * @return mixed
     */
    protected function getTable()
    {
        if (!is_array($this->parts['table']) && count($this->parts['table']) < 1) {
            trigger_error('No Table defined', E_USER_WARNING);
        }

        return reset($this->parts['table']);
    }

    /**
     * @return string
     */
    protected function parseHaving()
    {
        if (isset($this->parts['having'])) {
            return (string)$this->parts['having'];
        }

        return '';
    }

    /**
     * Parses ORDER BY entries.
     *
     * @return string
     */
    protected function parseOrder()
    {
        if (!isset($this->parts['order']) || !is_array($this->parts['order']) || count($this->parts['order']) < 1) {
            return false;
        }

        $orderParts = [];

        foreach ($this->parts['order'] as $itemOrder) {
            if ($itemOrder) {
                if (!is_array($itemOrder)) {
                    $itemOrder = [$itemOrder];
                }

                $column = isset($itemOrder[0]) ? $itemOrder[0] : false;
                $type = isset($itemOrder[1]) ? $itemOrder[1] : '';
                $protected = isset($itemOrder[2]) ? $itemOrder[2] : true;

                $column = ($protected ? $this->protect($column) : $column).' '.strtoupper($type);

                $orderParts[] = trim($column);
            }
        }

        return implode(', ', $orderParts);
    }

    /**
     * Adds backticks to input.
     *
     * @param string $input
     *
     * @return string
     */
    protected function protect($input)
    {
        return strpos($input, '(') !== false ? $input : str_replace('`*`', '*',
            '`'.str_replace('.', '`.`', $input).'`');
    }

    /**
     * Prefixes table names.
     *
     * @param string $table
     *
     * @return string
     */
    protected function tableName($table = '')
    {
        return $this->getManager()->tableName($table);
    }

    /**
     * Removes backticks from input.
     *
     * @param string $input
     *
     * @return string
     */
    protected function cleanProtected($input)
    {
        return str_replace('`', '', $input);
    }
}
