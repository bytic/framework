<?php

namespace Nip\Records\Relations;

use Nip\Database\Query\AbstractQuery;
use Nip\Database\Query\Delete as DeleteQuery;
use Nip\Database\Query\Insert as InsertQuery;
use Nip\Database\Query\Select as SelectQuery;
use Nip\HelperBroker;
use Nip\Records\Collections\Collection as RecordCollection;
use Nip\Records\Record;
use Nip\Records\Relations\Traits\HasPivotTable;

/**
 * Class HasAndBelongsToMany
 * @package Nip\Records\Relations
 */
class HasAndBelongsToMany extends HasOneOrMany
{
    use HasPivotTable;

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
        $query->from($this->getDB()->getDatabase() . '.' . $this->getTable());

        foreach ($this->getWith()->getFields() as $field) {
            $query->cols(["{$this->getWith()->getTable()}.$field", $field]);
        }

        foreach ($this->getJoinFields() as $field) {
            $query->cols(["{$this->getTable()}.$field", "__$field"]);
        }

        $this->hydrateQueryWithPivotConstraints($query);

        $order = $this->getParam('order');
        if ($order) {
            foreach ($order as $item) {
                $query->order([$item[0], $item[1]]);
            }
        }

        return $query;
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

    /**
     * @param null $joinFields
     */
    public function setJoinFields($joinFields)
    {
        $this->joinFields = $joinFields;
    }

    protected function initJoinFields()
    {
        $structure = $this->getDB()->describeTable($this->getTable());
        $this->setJoinFields(array_keys($structure["fields"]));
    }

    /**
     * @param AbstractQuery $query
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
     * Simple select query from the link table
     * @param bool $specific
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

    protected function deleteConnections()
    {
        $query = $this->newDeleteQuery();
        $query->where(
            "{$this->getManager()->getPrimaryFK()} = ?",
            $this->getItem()->{$this->getManager()->getPrimaryKey()}
        );
//        echo $query;
        $query->execute();
    }

    /**
     * @return DeleteQuery
     */
    protected function newDeleteQuery()
    {
        $query = $this->getDB()->newDelete();
        $query->table($this->getTable());
        return $query;
    }

    protected function saveConnections()
    {
        if ($this->hasResults()) {
            $query = $this->newInsertQuery();
            $this->queryAttachRecords($query, $this->getResults());
//            echo $query;
            $query->execute();
        }
    }

    /**
     * @return InsertQuery
     */
    protected function newInsertQuery()
    {
        $query = $this->getDB()->newInsert();
        $query->table($this->getTable());
        return $query;
    }

    /**
     * @param InsertQuery $query
     * @param $records
     */
    protected function queryAttachRecords($query, $records)
    {
        foreach ($records as $record) {
            $data = $this->formatAttachData($record);
            foreach ($this->getJoinFields() as $field) {
                if ($record->{"__$field"}) {
                    $data[$field] = $record->{"__$field"};
                } else {
                    $data[$field] = $data[$field] ? $data[$field] : false;
                }
            }
            $query->data($data);
        }
    }

    /**
     * @param $record
     * @return array
     */
    protected function formatAttachData($record)
    {
        $data = [
            $this->getManager()->getPrimaryFK() => $this->getItem()->{$this->getManager()->getPrimaryKey()},
            $this->getPivotFK() => $record->{$this->getWith()->getPrimaryKey()},
        ];
        return $data;
    }

    /**
     * @param $model
     */
    public function attach($model)
    {
        $query = $this->newInsertQuery();
        $this->queryAttachRecords($query, [$model]);
//            echo $query;
        $query->execute();
    }

    /**
     * @param Record $model
     */
    public function detach($model)
    {
        $query = $this->newDeleteQuery();
        $this->queryDetachRecords($query, [$model]);
//        echo $query;
        $query->execute();
    }

    /**
     * @param DeleteQuery $query
     * @param $records
     */
    protected function queryDetachRecords($query, $records)
    {
        $ids = HelperBroker::get('Arrays')->pluck($records, $this->getWith()->getPrimaryKey());
        $query->where(
            "{$this->getPivotFK()} IN ?",
            $ids
        );

        $query->where(
            "{$this->getManager()->getPrimaryFK()} = ?",
            $this->getItem()->{$this->getManager()->getPrimaryKey()}
        );
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
    protected function getDictionaryKey()
    {
        return '__' . $this->getFK();
    }
}
