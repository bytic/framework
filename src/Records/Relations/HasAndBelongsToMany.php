<?php

namespace Nip\Records\Relations;

use Nip\Database\Connection;
use Nip\Records\Collections\Collection as RecordCollection;
use Nip\Database\Query\Select as Query;

class HasAndBelongsToMany extends HasOneOrMany
{
    protected $_joinFields = null;


    public function newQuery()
    {
        $query = $this->getDB()->newQuery();

        $query->from($this->getWith()->getFullNameTable());
        $query->from($this->getDB()->getDatabase() . '.' . $this->getTable());

        foreach ($this->getWith()->getFields() as $field) {
            $query->cols(array("{$this->getWith()->getTable()}.$field", $field));
        }

        foreach ($this->getJoinFields() as $field) {
            $query->cols(array("{$this->getTable()}.$field", "__$field"));
        }

        $pk = $this->getWith()->getPrimaryKey();
        $fk = $this->getWith()->getPrimaryFK();
        $query->where("`{$this->getTable()}`.`$fk` = `{$this->getWith()->getTable()}`.`$pk`");

        $order = $this->getParam('order');
        if ($order) {
            foreach ($order as $item) {
                $query->order(array($item[0], $item[1]));
            }
        }

        return $query;
    }

    /**
     * @param Query $query
     * @return Query
     */
    public function populateQuerySpecific(Query $query)
    {
        $pk1 = $this->getManager()->getPrimaryKey();
        $fk1 = $this->getManager()->getPrimaryFK();

        $query->where("`{$this->getTable()}`.`$fk1` = ?", $this->getItem()->$pk1);

        return $query;
    }


    protected function getJoinFields()
    {
        if ($this->_joinFields == null) {
            $this->initJoinFields();
        }
        return $this->_joinFields;
    }

    protected function initJoinFields()
    {
        $structure = $this->getDB()->describeTable($this->getTable());
        $this->_joinFields = array_keys($structure["fields"]);
    }

    /**
     * Simple select query from the link table
     * @return Query
     */
    public function getLinkQuery($specific = true)
    {
        $pk = $this->getManager()->getPrimaryKey();
        $fk = $this->getManager()->getPrimaryFK();

        $query = $this->getDB()->newSelect();
        $query->from($this->getTable());

        if ($specific) {
            $query->where("`{$this->getTable()}`.`$fk` = ?", $this->getItem()->$pk);
        }

        return $query;
    }



    /**
     * @param RecordCollection $collection
     * @return RecordCollection
     */
    public function getEagerResults($collection)
    {
        if ($collection->count() < 1) {
            return $this->getWith()->newCollection();
        }
        $query = $this->getEagerQuery($collection);

        $return = $this->newCollection();
        $results = $this->getDB()->execute($query);
        if ($results->numRows() > 0) {
            $i = 1;
            while ($row = $results->fetchResult()) {
                $row['relation_key'] = $i++;
                $item = $this->getWith()->getNew($row);
                $return->add($item, 'relation_key');
            }
        }

        return $return;
    }

    protected function getDictionaryKey()
    {
        return '__'.$this->getFK();
    }

    public function save()
    {
        $this->_delete();
        $this->_save();

        return $this;
    }

    protected function _save()
    {
        if ($this->hasResults()) {
            $query = $this->getDB()->newQuery("insert");
            $query->table($this->getTable());
            $results = $this->getResults();

            foreach ($results as $item) {
                $data = array(
                    $this->getManager()->getPrimaryFK() => $this->getItem()->{$this->getManager()->getPrimaryKey()},
                    $this->getWith()->getPrimaryFK() => $item->{$this->getWith()->getPrimaryKey()}
                );
                foreach ($this->getJoinFields() as $field) {
                    if ($item->{"__$field"}) {
                        $data[$field] = $item->{"__$field"};
                    } else {
                        $data[$field] = $data[$field] ? $data[$field] : false;
                    }
                }
                $query->data($data);
            }

//            echo $query;
            $query->execute();
        }
    }

    protected function _delete()
    {
        $query = $this->getDB()->newQuery('delete');
        $query->table($this->getTable());
        $query->where("{$this->getManager()->getPrimaryFK()} = ?", $this->getItem()->{$this->getManager()->getPrimaryKey()});
//        echo $query;
        $query->execute();
    }


    /**
     * @return Connection
     */
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