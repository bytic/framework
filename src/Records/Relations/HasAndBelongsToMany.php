<?php

namespace Nip\Records\Relations;

use Nip\Database\Connection;
use Nip\Database\Query\AbstractQuery;
use Nip\Database\Query\Select as SelectQuery;
use Nip\Records\Collections\Collection as RecordCollection;

/**
 * Class HasAndBelongsToMany.
 */
class HasAndBelongsToMany extends HasOneOrMany
{
    /**
     * @var string
     */
    protected $type = 'hasAndBelongsToMany';

    /**
     * @var null
     */
    protected $joinFields = null;

    /** @noinspection PhpMissingParentCallCommonInspection
     * @return SelectQuery
     */
    public function newQuery()
    {
        $query = $this->getDB()->newSelect();

        $query->from($this->getWith()->getFullNameTable());
        $query->from($this->getDB()->getDatabase().'.'.$this->getTable());

        foreach ($this->getWith()->getFields() as $field) {
            $query->cols(["{$this->getWith()->getTable()}.$field", $field]);
        }

        foreach ($this->getJoinFields() as $field) {
            $query->cols(["{$this->getTable()}.$field", "__$field"]);
        }

        $pk = $this->getWith()->getPrimaryKey();
        $fk = $this->getWith()->getPrimaryFK();
        $query->where("`{$this->getTable()}`.`$fk` = `{$this->getWith()->getTable()}`.`$pk`");

        $order = $this->getParam('order');
        if ($order) {
            foreach ($order as $item) {
                $query->order([$item[0], $item[1]]);
            }
        }

        return $query;
    }

    /**
     * @return Connection
     */
    public function getDB()
    {
        return $this->getParam('link-db') == 'with' ? $this->getWith()->getDB() : parent::getDB();
    }

    /**
     * @param null $joinFields
     */
    public function setJoinFields($joinFields)
    {
        $this->joinFields = $joinFields;
    }

    /**
     * @param AbstractQuery $query
     *
     * @return AbstractQuery
     */
    public function populateQuerySpecific(AbstractQuery $query)
    {
        $pk1 = $this->getManager()->getPrimaryKey();
        $fk1 = $this->getManager()->getPrimaryFK();

        $query->where("`{$this->getTable()}`.`$fk1` = ?", $this->getItem()->{$pk1});

        return $query;
    }

    /**
     * Simple select query from the link table.
     *
     * @param bool $specific
     *
     * @return SelectQuery
     */
    public function getLinkQuery($specific = true)
    {
        $pk = $this->getManager()->getPrimaryKey();
        $fk = $this->getManager()->getPrimaryFK();

        $query = $this->getDB()->newSelect();
        $query->from($this->getTable());

        if ($specific) {
            $query->where("`{$this->getTable()}`.`$fk` = ?", $this->getItem()->{$pk});
        }

        return $query;
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @param RecordCollection $collection
     *
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

    /** @noinspection PhpMissingParentCallCommonInspection
     * @return $this
     */
    public function save()
    {
        $this->deleteConnections();
        $this->saveConnections();

        return $this;
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @return mixed
     */
    public function getWithClass()
    {
        return $this->getName();
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @return string
     */
    public function generateTable()
    {
        return $this->getCrossTable();
    }

    /**
     * Builds the name of a has-and-belongs-to-many association table.
     *
     * @return string
     */
    public function getCrossTable()
    {
        $tables = [$this->getManager()->getTable(), $this->getWith()->getTable()];
        sort($tables);

        return implode('-', $tables);
    }

    /**
     * @return null|array
     */
    protected function getJoinFields()
    {
        if ($this->joinFields == null) {
            $this->initJoinFields();
        }

        return $this->joinFields;
    }

    protected function initJoinFields()
    {
        $structure = $this->getDB()->describeTable($this->getTable());
        $this->setJoinFields(array_keys($structure['fields']));
    }

    protected function deleteConnections()
    {
        $query = $this->getDB()->newQuery('delete');
        $query->table($this->getTable());
        $query->where(
            "{$this->getManager()->getPrimaryFK()} = ?",
            $this->getItem()->{$this->getManager()->getPrimaryKey()}
        );
//        echo $query;
        $query->execute();
    }

    protected function saveConnections()
    {
        if ($this->hasResults()) {
            $query = $this->getDB()->newQuery('insert');
            $query->table($this->getTable());
            $results = $this->getResults();

            foreach ($results as $item) {
                $data = [
                    $this->getManager()->getPrimaryFK() => $this->getItem()->{$this->getManager()->getPrimaryKey()},
                    $this->getWith()->getPrimaryFK()    => $item->{$this->getWith()->getPrimaryKey()},
                ];
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

    /** @noinspection PhpMissingParentCallCommonInspection
     * @return string
     */
    protected function getDictionaryKey()
    {
        return '__'.$this->getFK();
    }
}
