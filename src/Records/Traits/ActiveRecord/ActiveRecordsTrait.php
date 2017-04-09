<?php

namespace Nip\Records\Traits\ActiveRecord;

use Nip\Database\Connections\Connection;
use Nip\Database\Query\AbstractQuery as Query;
use Nip\Database\Query\Delete as DeleteQuery;
use Nip\Database\Query\Insert as InsertQuery;
use Nip\Database\Query\Select as SelectQuery;
use Nip\Database\Query\Update as UpdateQuery;
use Nip\Database\Result;
use Nip\Paginator;
use Nip\Records\AbstractModels\Record;
use Nip\Records\Collections\Collection as RecordCollection;

/**
 * Class ActiveRecordsTrait
 * @package Nip\Records\Traits\ActiveRecord
 */
trait ActiveRecordsTrait
{

    /**
     * @var Connection
     */
    protected $connection = null;

    /**
     * @var null|string
     */
    protected $table = null;

    protected $tableStructure = null;

    /**
     * @var null|string
     */
    protected $primaryKey = null;
    protected $fields = null;
    protected $uniqueFields = null;

    /**
     * @var null|string
     */
    protected $foreignKey = null;

    /**
     * @return SelectQuery
     */
    public function newSelectQuery()
    {
        return $this->newQuery('select');
    }

    /**
     * Factory
     * @param string $type
     * @return Query|SelectQuery|InsertQuery|DeleteQuery|UpdateQuery
     */
    public function newQuery($type = 'select')
    {
        $query = $this->getDB()->newQuery($type);
        $query->cols("`" . $this->getTable() . "`.*");
        $query->from($this->getFullNameTable());
        $query->table($this->getTable());

        return $query;
    }

    /**
     * @return Connection
     */
    public function getDB()
    {
        if ($this->connection == null) {
            $this->initDB();
        }

        $this->checkDB();

        return $this->connection;
    }

    protected function initDB()
    {
        $this->setDB($this->newDbConnection());
    }

    /**
     * @param Connection $connection
     * @return $this
     */
    public function setDB($connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * @return Connection
     */
    protected function newDbConnection()
    {
        return app('db.connection');
    }

    public function checkDB()
    {
        if (!$this->hasDB()) {
            trigger_error("Database connection missing for [" . get_class($this) . "]", E_USER_ERROR);
        }
    }

    /**
     * @return bool
     */
    public function hasDB()
    {
        return $this->connection instanceof Connection;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        if ($this->table === null) {
            $this->initTable();
        }

        return $this->table;
    }

    /**
     * @param null $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    protected function initTable()
    {
        $this->setTable($this->generateTable());
    }

    /**
     * @return string
     */
    protected function generateTable()
    {
        return str_replace('-', '_', $this->getController());
    }

    /**
     * @return string
     */
    public function getFullNameTable()
    {
        $database = $this->getDB()->getDatabase();

        return $database ? $database . '.' . $this->getTable() : $this->getTable();
    }

    /**
     * @return null
     */
    public function getFields()
    {
        if ($this->fields === null) {
            $this->initFields();
        }

        return $this->fields;
    }

    public function initFields()
    {
        $structure = $this->getTableStructure();
        $this->fields = array_keys($structure['fields']);
    }

    /**
     * @return mixed
     */
    protected function getTableStructure()
    {
        if ($this->tableStructure == null) {
            $this->initTableStructure();
        }

        return $this->tableStructure;
    }

    /**
     * @param null $tableStructure
     */
    public function setTableStructure($tableStructure)
    {
        $this->tableStructure = $tableStructure;
    }

    protected function initTableStructure()
    {
        $this->setTableStructure($this->getDB()->getMetadata()->describeTable($this->getTable()));
    }

    /**
     * @return InsertQuery
     */
    public function newInsertQuery()
    {
        return $this->newQuery('insert');
    }

    /**
     * Updates a Record's database entry
     * @param Record $model
     * @return bool|Result
     */
    public function update(Record $model)
    {
        $query = $this->updateQuery($model);

        if ($query) {
            return $query->execute();
        }

        return false;
    }

    /**
     * @param Record $model
     * @return bool|UpdateQuery
     */
    public function updateQuery(Record $model)
    {
        $pk = $this->getPrimaryKey();
        if (!is_array($pk)) {
            $pk = [$pk];
        }

        $data = $this->getQueryModelData($model);

        if ($data) {
            $query = $this->newUpdateQuery();
            $query->data($data);

            foreach ($pk as $key) {
                $query->where("$key = ?", $model->{$key});
            }

            return $query;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        if ($this->primaryKey === null) {
            $this->initPrimaryKey();
        }

        return $this->primaryKey;
    }

    /**
     * @param null|string $primaryKey
     */
    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;
    }

    protected function initPrimaryKey()
    {
        $this->setPrimaryKey($this->generatePrimaryKey());
    }

    /**
     * @return string
     */
    public function generatePrimaryKey()
    {
        $structure = $this->getTableStructure();
        $primaryKey = false;
        if (is_array($structure) && isset($structure['indexes']['PRIMARY']['fields'])) {
            $primaryKey = $structure['indexes']['PRIMARY']['fields'];
            if (count($primaryKey) == 1) {
                $primaryKey = reset($primaryKey);
            }
        }

        return $primaryKey;
    }

    /**
     * @return UpdateQuery
     */
    public function newUpdateQuery()
    {
        return $this->newQuery('update');
    }

    /**
     * Saves a Record's database entry
     * @param Record $model
     * @return mixed
     */
    public function save(Record $model)
    {
        $pk = $this->getPrimaryKey();

        if (isset($model->{$pk})) {
            $model->update();

            return $model->{$pk};
        } else {
            /** @var Record $previous */
            $previous = $model->exists();

            if ($previous) {
                $data = $model->toArray();

                if ($data) {
                    $previous->writeData($model->toArray());
                }
                $previous->update();

                $model->writeData($previous->toArray());

                return $model->getPrimaryKey();
            }
        }

        $model->insert();

        return $model->getPrimaryKey();
    }

    /**
     * Delete a Record's database entry
     *
     * @param mixed|Record $input
     */
    public function delete($input)
    {
        $pk = $this->getPrimaryKey();

        if ($input instanceof $this->model) {
            $primary = $input->getPrimaryKey();
        } else {
            $primary = $input;
        }

        $query = $this->newDeleteQuery();
        $query->where("$pk = ?", $primary);
        $query->limit(1);

        $this->getDB()->execute($query);
    }

    /**
     * @return DeleteQuery
     */
    public function newDeleteQuery()
    {
        return $this->newQuery('delete');
    }

    /**
     * Delete a Record's database entry
     * @param array $params
     * @return $this
     */
    public function deleteByParams($params = [])
    {
        extract($params);

        $query = $this->newDeleteQuery();

        if (isset($where)) {
            if (is_array($where)) {
                foreach ($where as $condition) {
                    $condition = (array)$condition;
                    $query->where($condition[0], $condition[1]);
                }
            } else {
                call_user_func_array([$query, 'where'], $where);
            }
        }

        if (isset($order)) {
            call_user_func_array([$query, 'order'], $order);
        }

        if (isset($limit)) {
            call_user_func_array([$query, 'limit'], $limit);
        }

        $this->getDB()->execute($query);

        return $this;
    }

    /**
     * Returns paginated results
     * @param Paginator $paginator
     * @param array $params
     * @return mixed
     */
    public function paginate(Paginator $paginator, $params = [])
    {
        $query = $this->paramsToQuery($params);

        $countQuery = $this->getDB()->newSelect();
        $countQuery->count(['*', 'count']);
        $countQuery->from([$query, 'tbl']);
        $results = $countQuery->execute()->fetchResults();
        $count = $results[0]['count'];

        $paginator->setCount($count);

        $params['limit'] = $paginator->getLimits();

        return $this->findByParams($params);
    }

    /**
     * @param array $params
     * @return SelectQuery
     */
    public function paramsToQuery($params = [])
    {
        $this->injectParams($params);

        $query = $this->newQuery('select');
        $query->addParams($params);

        return $query;
    }

    /**
     * @param array $params
     */
    protected function injectParams(&$params = [])
    {
    }

    /**
     * Checks the registry before fetching from the database
     * @param mixed $primary
     * @return Record
     */
    public function findOne($primary)
    {
        $item = $this->getRegistry()->get($primary);
        if (!$item) {
            $all = $this->getRegistry()->get("all");
            if ($all) {
                $item = $all[$primary];
            }
            if (!$item) {
                $params['where'][] = ["`{$this->getTable()}`.`{$this->getPrimaryKey()}` = ?", $primary];
                $item = $this->findOneByParams($params);
                if ($item) {
                    $this->getRegistry()->set($primary, $item);
                }

                return $item;
            }
        }

        return $item;
    }

    /**
     * @param Query $query
     * @param array $params
     * @return bool
     */
    public function findOneByQuery($query, $params = [])
    {
        $query->limit(1);
        $return = $this->findByQuery($query, $params);
        if (count($return) > 0) {
            return $return->rewind();
        }

        return false;
    }

    /**
     * @param $query
     * @param array $params
     * @return RecordCollection
     */
    public function findByQuery($query, $params = [])
    {
        $return = $this->newCollection();

        $results = $this->getDB()->execute($query);
        if ($results->numRows() > 0) {
            $pk = $this->getPrimaryKey();
            /** @noinspection PhpAssignmentInConditionInspection */
            while ($row = $results->fetchResult()) {
                $item = $this->getNew($row);
                if (is_string($pk)) {
                    $this->getRegistry()->set($item->getPrimaryKey(), $item);
                }
                if (isset($params['indexKey']) && !empty($params['indexKey'])) {
                    $return->add($item, $params['indexKey']);
                } else {
                    $return->add($item);
                }
            }
        }

        return $return;
    }

    /**
     * @param bool|array $where
     * @return int
     */
    public function count($where = false)
    {
        return $this->countByParams(["where" => $where]);
    }

    /**
     * Counts all the Record entries in the database
     * @param array $params
     * @return int
     */
    public function countByParams($params = [])
    {
        $this->injectParams($params);
        $query = $this->newQuery('select');
        $query->addParams($params);

        return $this->countByQuery($query);
    }

    /**
     * Counts all the Record entries in the database
     * @param Query $query
     * @return int
     */
    public function countByQuery($query)
    {
        $queryCount = clone $query;
        $queryCount->setCols('count(*) as count');
        $result = $this->getDB()->execute($queryCount);

        if ($result->numRows()) {
            $row = $result->fetchResult();

            return (int)$row['count'];
        }

        return false;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function cleanData($data)
    {
        return $this->getDB()->getAdapter()->cleanData($data);
    }

    /**
     * @param $name
     * @param $arguments
     * @return RecordCollection|null
     */
    protected function isCallDatabaseOperation($name, $arguments)
    {
        $operations = ["find", "delete", "count"];
        foreach ($operations as $operation) {
            if (strpos($name, $operation . "By") !== false || strpos($name, $operation . "OneBy") !== false) {
                $params = [];
                if (count($arguments) > 1) {
                    $params = end($arguments);
                }

                $match = str_replace([$operation . "By", $operation . "OneBy"], "", $name);
                $field = inflector()->underscore($match);

                if ($field == $this->getPrimaryKey()) {
                    return $this->findByPrimary($arguments[0]);
                }

                $params['where'][] = ["$field " . (is_array($arguments[0]) ? "IN" : "=") . " ?", $arguments[0]];

                $operation = str_replace($match, "", $name) . "Params";

                return $this->$operation($params);
            }
        }

        return null;
    }
}
