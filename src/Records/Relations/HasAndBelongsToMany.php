<?php

namespace Nip\Records\Relations;

class HasAndBelongsToMany extends HasOneOrMany
{
    protected $_joinFields = array();

    protected function _getJoinFields()
    {
        if (!$this->_joinFields) {
            $structure = $this->getDB()->describeTable($this->getParam("table"));
            $this->_joinFields = array_keys($structure["fields"]);
        }
        return $this->_joinFields;
    }

    /**
     * When the $specific is false, it returns the generic query that applies
     * to any item, not just this collection's item
     *
     * @return Nip_DB_Query_Select
     */
    public function getQuery($specific = true)
    {
        if (!$this->_query) {
            list($pk1, $fk1) = array($this->getManager()->getPrimaryKey(), $this->getManager()->getPrimaryFK());
            list($pk2, $fk2) = array($this->getWith()->getPrimaryKey(), $this->getWith()->getPrimaryFK());

            $query = $this->getDB()->newQuery();

            $query->from($this->getWith()->getFullNameTable());
            $query->from($this->getDB()->getDatabase() . '.' . $this->getParam("table"));

            foreach ($this->getWith()->getFields() as $field) {
                $query->cols(array("{$this->getWith()->getTable()}.$field", $field));
            }

            foreach ($this->_getJoinFields() as $field) {
                $query->cols(array("{$this->getParam("table")}.$field", "__$field"));
            }

            if ($specific) {
                $query->where("`{$this->getParam("table")}`.`$fk1` = ?", $this->getItem()->$pk1);
            }

            $query->where("`{$this->getParam("table")}`.`$fk2` = `{$this->getWith()->getTable()}`.`$pk2`");

            $order = $this->getParam('order');
            if ($order) {
                foreach ($order as $item) {
                    $query->order(array($item[0], $item[1]));
                }
            }

            $this->_query = $query;
        }

        return $this->_query;
    }

    /**
     * Simple select query from the link table
     * @return Nip_DB_Query_Select
     */
    public function getLinkQuery($specific = true)
    {
        list($pk1, $fk1) = array($this->getManager()->getPrimaryKey(), $this->getManager()->getPrimaryFK());

        $query = $this->getDB()->newQuery();
        $query->from($this->getParam("table"));

        if ($specific) {
            $query->where("`{$this->getParam("table")}`.`$fk1` = ?", $this->getItem()->$pk1);
        }

        return $query;
    }

    public function save()
    {
        $this->_delete();
        $this->_save();

        return $this;
    }

    protected function _save()
    {
        if (count($this)) {
            $query = $this->getDB()->newQuery("insert");
            $query->table($this->getParam("table"));

            foreach ($this as $item) {
                $data = array(
                    $this->getManager()->getPrimaryFK() => $this->getItem()->{$this->getManager()->getPrimaryKey()},
                    $this->getWith()->getPrimaryFK() => $item->{$this->getWith()->getPrimaryKey()}
                );
                foreach ($this->_getJoinFields() as $field) {
                    if ($item->{"__$field"}) {
                        $data[$field] = $item->{"__$field"};
                    } else {
                        $data[$field] = $data[$field] ? $data[$field] : false;
                    }
                }
                $query->data($data);
            }

            $query->execute();
            //echo $query;
        }
    }

    protected function _delete()
    {
        $query = $this->getDB()->newQuery('delete');
        $query->table($this->getParam("table"));
        $query->where("{$this->getManager()->getPrimaryFK()} = ?", $this->getItem()->{$this->getManager()->getPrimaryKey()});
        //echo $query;
        $query->execute();
    }


    public function getDB()
    {
        return $this->getParam("link-db") == 'with' ? $this->getWith()->getDB() : parent::getDB();
    }

    public function initTable()
    {
        $tableName = $this->getCrossTable();
        $this->setTable($tableName);
    }

    /**
     * Builds the name of a has-and-belongs-to-many association table
     * @return string
     */
    public function getCrossTable()
    {
        $tables = array($this->getManager()->getTable(), $this->getWith()->getTable());
        sort($tables);
        return implode("-", $tables);
    }
}