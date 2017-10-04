<?php


namespace Nip\Database\Query;

/**
 * Class Insert
 * @package Nip\Database\Query
 */
class Insert extends AbstractQuery
{
    protected $_cols;

    protected $_values;

    /**
     * @return string
     */
    public function assemble()
    {
        $return = "INSERT INTO " . $this->protect($this->getTable());
        $return .= $this->parseCols();
        $return .= $this->parseValues();
        $return .= $this->parseOnDuplicate();

        return $return;
    }

    /**
     * @return string
     */
    public function parseCols()
    {
        if (is_array($this->parts['data'][0])) {
            $this->setCols(array_keys($this->parts['data'][0]));
        }
        return $this->_cols ? ' (' . implode(',', array_map([$this, 'protect'], $this->_cols)) . ')' : '';
    }

    /**
     * @param array|string $cols
     * @return $this
     */
    public function setCols($cols = null)
    {
        $this->_cols = $cols;
        return $this;
    }

    /**
     * @return string|false
     */
    public function parseValues()
    {
        if ($this->_values instanceof AbstractQuery) {
            return ' ' . (string) $this->_values;
        } elseif (is_array($this->parts['data'])) {
            return $this->parseData();
        }
        return false;
    }

    /**
     * Parses INSERT data
     *
     * @return string
     */
    protected function parseData()
    {
        $values = [];
        foreach ($this->parts['data'] as $key => $data) {
            foreach ($data as $value) {
                if (!is_array($value)) {
                    $value = [$value];
                }

                foreach ($value as $insertValue) {
                    $values[$key][] = $this->getManager()->getAdapter()->quote($insertValue);
                }
            }
        }
        foreach ($values as &$value) {
            $value = "(" . implode(", ", $value) . ")";
        }

        return ' VALUES ' . implode(', ', $values);
    }

    /**
     * @return string
     */
    public function parseOnDuplicate()
    {
        if ($this->hasPart('onDuplicate')) {
            $update = $this->getManager()->newUpdate();

            $onDuplicates = $this->getPart('onDuplicate');
            $data = [];
            foreach ($onDuplicates as $onDuplicate) {
                foreach ($onDuplicate as $key => $value) {
                    $data[$key] = $value;
                }
            }
            $update->data($data);

            return " ON DUPLICATE KEY UPDATE {$update->parseUpdate()}";
        }
        return '';
    }

    public function setValues($values)
    {
        $this->_values = $values;

        return $this;
    }

    public function onDuplicate($value)
    {
        $this->addPart('onDuplicate', $value);
    }
}
