<?php

abstract class Nip_Records extends \Nip\Records\_Abstract\Table {

    protected $_db;
    protected $_collectionClass = 'Nip_RecordCollection';
    protected $_table;
    protected $_tableStructure = NULL;
    protected $_primaryKey;
    protected $_helpers = array();
    protected $_fields = array();

    public function __construct() {
        parent::__construct();

        $this->setUpDB();
        $this->setUpStructure();
    }

    /**
     * Overloads findByRecord, findByField, deleteByRecord, deleteByField, countByRecord, countByField
     *
     * @example findByCategory(Category $item)
     * @example deleteByProduct(Product $item)
     * @example findByIdUser(2)
     * @example deleteByTitle(array('Lorem ipsum', 'like'))
     * @example countByIdCategory(1)
     *
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments) {
        $operations = array("find", "delete", "count");
        foreach ($operations as $operation) {
            if (strpos($name, $operation . "By") !== false || strpos($name, $operation . "OneBy") !== false) {
                $params = array();
                if (count($arguments) > 1) {
                    $params = end($arguments);
                }

                $match = str_replace(array($operation . "By", $operation . "OneBy"), "", $name);
                $field = inflector()->underscore($match);

                if ($field == $this->getPrimaryKey()) {
                    return $this->findByPrimary($arguments[0]);
                }

                $params['where'][] = array("$field " . (is_array($arguments[0]) ? "IN" : "=") . " ?", $arguments[0]);

                $operation = str_replace($match, "", $name) . "Params";
                return $this->$operation($params);
            }
        }

        if ($name === ucfirst($name)) {
            return \Nip\HelperBroker::get($name);
        }

        trigger_error("Call to undefined method $name", E_USER_ERROR);
    }

    public function __wakeup() {
        $this->setUpDB();
    }

    /**
     * Factory
     * @return Nip_DB_Query_Abstract
     */
    public function newQuery($type = 'select') {
        $query = $this->getDB()->newQuery($type);
        $query->cols("`$this->_table`.*");
        $query->from($this->getDB()->getDatabase().'.'.$this->_table);
        $query->table($this->_table);
        return $query;
    }

    /**
     * Inserts foreign connections into Query according to the class relationships
     *
     * @param Nip_DB_Query $query
     * @param string $class
     */
    public function associateIntoQuery(Nip_DB_Query_Abstract $query, $class) {
        $manager = call_user_func(array($class, "instance"));

        $query->from($manager->getTable());

        if ($this->hasAndBelongsToMany($class)) {
            $crossTable = $this->getCrossTable($this, $manager);
            $query->from($crossTable);

            $query->where("{$this->getTable()}.{$this->getPrimaryKey()} = {$crossTable}.{$this->getPrimaryFK()}");
            $query->where("{$manager->getTable()}.{$manager->getPrimaryKey()} = {$crossTable}.{$manager->getPrimaryFK()}");
        }
    }

    /**
     * Factory
     *
     * @return Record
     * @param array $data[optional]
     */
    public function getNew($data = array()) {

        $pk = $this->getPrimaryKey();
        if (is_string($pk) && $this->getRegistry()->get($data[$pk])) {
            $return = $this->getRegistry()->get($data[$pk]);
            $return->writeData($data);
            return $return;
        }

        $record = $this->getNewRecord($data);

        return $record;
    }

    protected function setUpDB() {
        $this->_db = db();
    }

    protected function setUpStructure() {
        if ($this->_tableStructure === NULL) {
            $this->_tableStructure = $this->getDB()->getMetadata()->describeTable($this->_table);
            $this->_fields = array_keys($this->_tableStructure['fields']);

            $this->_primaryKey = $this->_tableStructure['indexes']['PRIMARY']['fields'];
            if (count($this->_primaryKey) == 1) {
                $this->_primaryKey = reset($this->_primaryKey);
            }
        }
    }

    /**
     * Sets model and database table from the class name
     */
    protected function inflect() {
        parent::inflect();

        if (!$this->_table) {
            $this->_table = $this->_controller;
        }
    }

    /**
     * Inserts a Record into the database
     * @param Nip_Record $model
     * @param array $onDuplicate
     * @return mixed
     */
    public function insert($model, $onDuplicate = false) {
        $query = $this->insertQuery($model, $onDuplicate);
        $query->execute();

        return $this->getDB()->lastInsertID();
    }

    public function insertQuery($model, $onDuplicate) {
        $inserts = array();

        $fields = $model->getFields() ? $model->getFields() : $this->getFields();
        foreach ($fields as $field) {
            $inserts[$field] = $model->$field;
        }

        $query = $this->newQuery('insert');
        $query->data($inserts);

        if ($onDuplicate !== false) {
            $query->onDuplicate($onDuplicate);
        }

        return $query;
    }

    /**
     * Updates a Record's database entry
     * @param Nip_Record $model
     */
    public function update($model) {
        $query = $this->updateQuery($model);

        if ($query) {
            return $query->execute();
        }
        return false;
    }

    public function updateQuery($model) {
        $pk = $this->getPrimaryKey();
        if (!is_array($pk)) {
            $pk = array($pk);
        }

        $data = array();
        $fields = $model->getFields() ? $model->getFields() : $this->getFields();
        foreach ($fields as $field) {
            $data[$field] = $model->$field;
        }

        if ($data) {
            $query = $this->newQuery('update');
            $query->data($data);

            foreach ($pk as $key) {
                $query->where("$key = ?", $model->$key);
            }

            return $query;
        }

        return false;
    }

    /**
     * Saves a Record's database entry
     * @param Record $model
     */
    public function save($model) {
        $pk = $this->getPrimaryKey();

        if (isset($model->$pk)) {
            $model->update();
            return $model->$pk;
        } else {
            $previous = $model->exists();

            if ($previous) {
                $data = $model->toArray();

                if ($data) {
                    $previous->writeData($model->toArray());
                }
                $previous->update();

                $model->writeData($previous->toArray());

                return $model->$pk;
            }
        }

        $model->insert();
        return $model->$pk;
    }

    /**
     * Delete a Record's database entry
     *
     * @param mixed $input
     */
    public function delete($input) {
        $pk = $this->getPrimaryKey();

        if ($input instanceof $this->_model) {
            $primary = $input->$pk;
        } else {
            $primary = $input;
        }

        $query = $this->newQuery('delete');
        $query->where("$pk = ?", $primary);
        $query->limit(1);

        $this->getDB()->execute($query);
    }

    /**
     * Delete a Record's database entry
     * @param mixed $input
     */
    public function deleteByParams(array $params = array()) {
        extract($params);

        $query = $this->newQuery('delete');

        if (isset($where)) {
            if (is_array($where)) {
                foreach ($where as $condition) {
                    $condition = (array) $condition;
                    $query->where($condition[0], $condition[1]);
                }
            } else {
                call_user_func_array(array($query, 'where'), $where);
            }
        }

        if (isset($order)) {
            call_user_func_array(array($query, 'order'), $order);
        }

        if (isset($limit)) {
            call_user_func_array(array($query, 'limit'), $limit);
        }

        $results = $this->getDB()->execute($query);
    }

    /**
     * Returns paginated results
     *
     * @param Nip_Paginator $paginator
     * @param array $params
     */
    public function paginate(Nip_Paginator $paginator, $params = array()) {
        $query = $this->paramsToQuery($params);

        $countQuery = $this->getDB()->newQuery('select');
        $countQuery->count(array('*', 'count'));
        $countQuery->from(array($query, 'tbl'));
        $results = $countQuery->execute()->fetchResults();
        $count = $results[0]['count'];

        $paginator->setCount($count);

        $params['limit'] = $paginator->getLimits();

        return $this->findByParams($params);
    }

    /**
     * Finds all the Records
     *
     * @return array
     */
    public function findAll() {
        return $this->findByParams();
    }

    protected function injectParams(&$params = array()) {

    }

    /**
     * Finds one Record using primary key
     *
     * @param mixed $primary
     * @return Nip_Record
     */
    public function findOne($primary) {
        if ($primary) {
            $item = $this->getRegistry()->get($primary);
            if (!$item) {
                $params['where'][] = array("`{$this->_table}`.`{$this->getPrimaryKey()}` = ?", $primary);
                return $this->findOneByParams($params);
            }
        }
        return false;
    }

    /**
     * Finds one Record using params array
     *
     * @param array $params
     * @return Nip_Record
     */
    public function findOneByParams(array $params = array()) {
        $params['limit'] = 1;
        $records = $this->findByParams($params);
        if (count($records) > 0) {
            return $records->rewind();
        }
        return false;
    }

    /**
     * Finds Records using params array
     *
     * @param array $params
     * @return mixed
     */
    public function findByParams($params = array()) {
        $query = $this->paramsToQuery($params);
        return $this->findByQuery($query, $params);
    }

    public function paramsToQuery($params = array()) {
        $this->injectParams($params);
        extract($params);

        $query = $this->newQuery('select');
        call_user_func_array(array($query, 'cols'), (array) $select);

        if (isset($from)) {
            call_user_func_array(array($query, 'from'), $from);
        }

        if (is_array($where)) {
            foreach ($where as $condition) {
                $condition = (array) $condition;
                $query->where($condition[0], $condition[1]);
            }
        }

        if (isset($order)) {
            call_user_func_array(array($query, 'order'), $order);
        }

        if (isset($group)) {
            call_user_func_array(array($query, 'group'), array($group));
        }

        if (isset($having)) {
            call_user_func_array(array($query, 'having'), $having);
        }

        if (isset($limit)) {
            call_user_func_array(array($query, 'limit'), (array) $limit);
        }

        return $query;
    }

    public function findOneByQuery($query, $params = array()) {
        $query->limit(1);
        $return = $this->findByQuery($query, $params);
        if (count($return) > 0) {
            return $return->rewind();
        }
        return false;
    }

    public function findByQuery($query, $params = array()) {
        $class = $this->getCollectionClass();
        $return = new $class();

        $results = $this->getDB()->execute($query);
        if ($results->numRows() > 0) {
            $pk = $this->getPrimaryKey();
            while ($row = $results->fetchResult()) {
                $item = $this->getNew($row);
                if (is_string($pk)) {
                    $this->getRegistry()->set($item->$pk, $item);
                }
                if ($params['indexKey']) {
                    $return->add($item, $params['indexKey']);
                } else {
                    $return->add($item);
                }
            }
        }

        return $return;
    }

    public function count($where = false) {
        return $this->countByParams(array("where" => $where));
    }

    /**
     * Counts all the Record entries in the database
     * @return int
     */
    public function countByParams($params = array()) {
        $this->injectParams($params);
        extract($params);

        $return = 0;

        $query = $this->newQuery('select');

        if (isset($from)) {
            call_user_func_array(array($query, 'from'), $from);
        }

        if (is_array($where)) {
            foreach ($where as $condition) {
                $condition = (array) $condition;
                $query->where($condition[0], $condition[1]);
            }
        }

        return $this->countByQuery($query);
    }

    /**
     * Counts all the Record entries in the database
     * @return int
     */
    public function countByQuery($query) {
        $query->setCols('count(*) as count');

        /* @var $result DBResult */
        $result = $this->getDB()->execute($query);

        if ($result->numRows()) {
            $row = $result->fetchResult();
            $return = (int) $row['count'];
        }

        return $return;
    }

    public function cleanData($data) {
        return $this->getDB()->cleanData($data);
    }

    public function getDB() {
        return $this->_db;
    }

    public function getTable() {
        return $this->_table;
    }

    public function getFullNameTable() {
        return $this->getDB()->getDatabase().'.'.$this->getTable();
    }

    public function getCollectionClass() {
        return $this->_collectionClass;
    }

    public function getPrimaryKey() {
        return $this->_primaryKey;
    }

    public function getFields() {
        return $this->_fields;
    }

    public function getUniqueFields() {
        $return = array();
        foreach ($this->_tableStructure['indexes'] as $name => $index) {
            if ($index['unique']) {
                foreach ($index['fields'] as $field) {
                    if ($field != $this->getPrimaryKey()) {
                        $return[] = $field;
                    }
                }
            }
        }
        return $return;
    }

    public function getFullTextFields() {
        $return = array();
        foreach ($this->_tableStructure['indexes'] as $name => $index) {
            if ($index['fulltext']) {
                $return[$name] = $index['fields'];
            }
        }
        return $return;
    }

    /**
     * @return Nip_Db_Records_Cache
     */
    public function getCacheManager() {
        if (!$this->_cacheManager) {
            $this->_cacheManager = new Nip_Db_Records_Cache();
            $this->_cacheManager->setManager($this);
        }

        return $this->_cacheManager;
    }
}