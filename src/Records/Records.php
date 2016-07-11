<?php

abstract class Nip_Records extends \Nip\Records\_Abstract\Table
{

    protected $_db = null;

    protected $_collectionClass = 'Nip_RecordCollection';
    protected $_helpers = array();

    protected $_table = null;
    protected $_tableStructure = null;
    protected $_primaryKey = null;
    protected $_fields = null;
    protected $_uniqueFields = null;

    public function __construct()
    {
        parent::__construct();

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
    public function __call($name, $arguments)
    {
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
            return $this->getHelper($name);
        }

        trigger_error("Call to undefined method $name", E_USER_ERROR);
    }

    public function __wakeup()
    {
        $this->setUpDB();
    }

    public function getHelper($name)
    {
        return \Nip\HelperBroker::get($name);
    }


    public function exists(Record $item)
    {
        $params = array();
        $params['where'] = array();

        $fields = $this->getUniqueFields();

        if (!$fields) {
            return false;
        }

        foreach ($fields as $field) {
            $params['where'][$field . '-UNQ'] = array("$field = ?", $item->$field);
        }

        $pk = $this->getPrimaryKey();
        if ($item->$pk) {
            $params['where'][] = array("$pk != ?", $item->$pk);
        }

        return $this->findOneByParams($params);
    }

    /**
     * When searching by primary key, look for items in current registry before
     * fetching them from the database
     *
     * @param array $pk_list
     * @return Nip_RecordCollection
     */
    public function findByPrimary($pk_list = array())
    {
        $pk = $this->getPrimaryKey();
        $return = new Nip_RecordCollection();

        if ($pk_list) {
            $pk_list = array_unique($pk_list);
            foreach ($pk_list as $key => $value) {
                $item = $this->getRegistry()->get($value);
                if ($item) {
                    unset($pk_list[$key]);
                    $return[$item->$pk] = $item;
                }
            }
            if ($pk_list) {
                $query = $this->paramsToQuery();
                $query->where("$pk IN ?", $pk_list);
                $items = $this->findByQuery($query);

                if (count($items)) {
                    foreach ($items as $item) {
                        $this->getRegistry()->set($item->$pk, $item);
                        $return[$item->$pk] = $item;
                    }
                }
            }
        }

        return $return;
    }

    /**
     * @return Nip_RecordCollection
     */
    public function getAll()
    {
        if (!$this->getRegistry()->exists("all")) {
            $this->getRegistry()->set("all", $this->findAll());
        }
        return $this->getRegistry()->get("all");
    }

    /**
     * @return Nip_RecordCollection
     */
    public function findAll()
    {
        return $this->findByParams();
    }

    public function findLast($count = 9)
    {
        return $this->findByParams(array(
            'limit' => $count,
        ));
    }

    /**
     * Factory
     * @return Nip_DB_Query_Abstract
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
     * Inserts foreign connections into Query according to the class relationships
     *
     * @param Nip_DB_Query $query
     * @param string $class
     */
    public function associateIntoQuery(Nip_DB_Query_Abstract $query, $class)
    {
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
     * @param array $data [optional]
     */
    public function getNew($data = array())
    {

        $pk = $this->getPrimaryKey();
        if (is_string($pk) && $this->getRegistry()->get($data[$pk])) {
            $return = $this->getRegistry()->get($data[$pk]);
            $return->writeData($data);
            $return->writeDBData($data);
            return $return;
        }

        $record = $this->getNewRecordFromDB($data);

        return $record;
    }

    public function getDB()
    {
        if ($this->_db == null) {
            $this->setUpDB();
        }
        return $this->_db;
    }

    protected function setUpDB()
    {
        $this->_db = db();
    }


    protected function getTableStructure()
    {
        if ($this->_tableStructure == null) {
            $this->initTable();
        }
        return $this->_tableStructure;
    }

    protected function initTableStructure()
    {
        $this->_tableStructure = $this->getDB()->getMetadata()->describeTable($this->_table);
    }

    /**
     * Inserts a Record into the database
     * @param Nip_Record $model
     * @param array $onDuplicate
     * @return mixed
     */
    public function insert($model, $onDuplicate = false)
    {
        $query = $this->insertQuery($model, $onDuplicate);
        $query->execute();

        return $this->getDB()->lastInsertID();
    }

    public function insertQuery($model, $onDuplicate)
    {
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
    public function update($model)
    {
        $query = $this->updateQuery($model);

        if ($query) {
            return $query->execute();
        }
        return false;
    }

    public function updateQuery($model)
    {
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
    public function save($model)
    {
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
    public function delete($input)
    {
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
    public function deleteByParams(array $params = array())
    {
        extract($params);

        $query = $this->newQuery('delete');

        if (isset($where)) {
            if (is_array($where)) {
                foreach ($where as $condition) {
                    $condition = (array)$condition;
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
    public function paginate(Nip_Paginator $paginator, $params = array())
    {
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

    protected function injectParams(&$params = array())
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
                $params['where'][] = array("`{$this->getTable()}`.`{$this->getPrimaryKey()}` = ?", $primary);
                return $this->findOneByParams($params);
                if ($item) {
                    $this->getRegistry()->set($primary, $item);
                }
            }
        }
        return $item;
    }

    /**
     * Finds one Record using params array
     *
     * @param array $params
     * @return Nip_Record
     */
    public function findOneByParams(array $params = array())
    {
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
    public function findByParams($params = array())
    {
        $query = $this->paramsToQuery($params);
        return $this->findByQuery($query, $params);
    }

    public function paramsToQuery($params = array())
    {
        $this->injectParams($params);

        $query = $this->newQuery('select');
        call_user_func_array(array($query, 'cols'), (array)$params['select']);

        if (isset($from)) {
            call_user_func_array(array($query, 'from'), $params['from']);
        }

        if (is_array($params['where'])) {
            foreach ($params['where'] as $condition) {
                $condition = (array)$condition;
                $query->where($condition[0], $condition[1]);
            }
        }

        if (isset($params['order'])) {
            call_user_func_array(array($query, 'order'), $params['order']);
        }

        if (isset($params['group'])) {
            call_user_func_array(array($query, 'group'), array($params['group']));
        }

        if (isset($params['having'])) {
            call_user_func_array(array($query, 'having'), $params['having']);
        }

        if (isset($params['limit'])) {
            call_user_func_array(array($query, 'limit'), (array)$params['limit']);
        }

        return $query;
    }

    public function findOneByQuery($query, $params = array())
    {
        $query->limit(1);
        $return = $this->findByQuery($query, $params);
        if (count($return) > 0) {
            return $return->rewind();
        }
        return false;
    }

    public function findByQuery($query, $params = array())
    {
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

    public function count($where = false)
    {
        return $this->countByParams(array("where" => $where));
    }

    /**
     * Counts all the Record entries in the database
     * @return int
     */
    public function countByParams($params = array())
    {
        $this->injectParams($params);

        $query = $this->newQuery('select');

        if (isset($params['from'])) {
            call_user_func_array(array($query, 'from'), $params['from']);
        }

        if (is_array($params['where'])) {
            foreach ($params['where'] as $condition) {
                $condition = (array)$condition;
                $query->where($condition[0], $condition[1]);
            }
        }

        return $this->countByQuery($query);
    }

    /**
     * Counts all the Record entries in the database
     * @return int
     */
    public function countByQuery($query)
    {
        $query->setCols('count(*) as count');

        /* @var $result DBResult */
        $result = $this->getDB()->execute($query);

        if ($result->numRows()) {
            $row = $result->fetchResult();
            $return = (int)$row['count'];
        }

        return $return;
    }

    public function cleanData($data)
    {
        return $this->getDB()->cleanData($data);
    }

    public function getTable()
    {
        if ($this->_table === null) {
            $this->initTable();
        }
        return $this->_table;
    }

    public function initTable()
    {
        $this->_table = $this->getController();
    }

    public function getFullNameTable()
    {
        return $this->getDB()->getDatabase() . '.' . $this->getTable();
    }

    public function getCollectionClass()
    {
        return $this->_collectionClass;
    }

    /**
     * The name of the field used as a foreign key in other tables
     * @return string
     */
    public function getPrimaryFK()
    {
        if (!$this->_foreignKey) {
            $this->_foreignKey = $this->getPrimaryKey() . "_" . inflector()->underscore($this->getModel());
        }
        return $this->_foreignKey;
    }

    /**
     * The name of the field used as a foreign key in other tables
     * @return string
     */
    public function getUrlPK()
    {
        if (!$this->_urlPK) {
            $this->_urlPK = $this->getPrimaryKey();
        }
        return $this->_urlPK;
    }

    public function getPrimaryKey()
    {
        if ($this->_primaryKey === null) {
            $this->initPrimaryKey();
        }
        return $this->_primaryKey;
    }

    protected function initPrimaryKey()
    {
        $structure = $this->getTableStructure();
        $this->_primaryKey = $structure['indexes']['PRIMARY']['fields'];
        if (count($this->_primaryKey) == 1) {
            $this->_primaryKey = reset($this->_primaryKey);
        }
    }

    public function getFields()
    {
        if ($this->_fields === null) {
            $this->initPrimaryKey();
        }
        return $this->_fields;
    }

    public function initFields()
    {
        $structure = $this->getTableStructure();
        $this->_fields = array_keys($structure['fields']);
    }

    public function getUniqueFields()
    {
        if ($this->_uniqueFields === null) {
            $this->initUniqueFields();
        }
        return $this->_uniqueFields;
    }

    public function initUniqueFields()
    {
        $this->_uniqueFields = array();
        $structure = $this->getTableStructure();
        foreach ($structure['indexes'] as $name => $index) {
            if ($index['unique']) {
                foreach ($index['fields'] as $field) {
                    if ($field != $this->getPrimaryKey()) {
                        $this->_uniqueFields[] = $field;
                    }
                }
            }
        }
        return $this->_uniqueFields;
    }

    public function getFullTextFields()
    {
        $return = array();
        $structure = $this->getTableStructure();
        foreach ($structure['indexes'] as $name => $index) {
            if ($index['fulltext']) {
                $return[$name] = $index['fields'];
            }
        }
        return $return;
    }

    /**
     * @return Nip_Db_Records_Cache
     */
    public function getCacheManager()
    {
        if (!$this->_cacheManager) {
            $this->initCacheManager();
        }

        return $this->_cacheManager;
    }

    public function initCacheManager()
    {
        $this->_cacheManager = $this->newCacheManager();
        $this->_cacheManager->setManager($this);
    }


    /**
     * @return Nip_Db_Records_Cache
     */
    public function newCacheManager()
    {
        return new Nip_Db_Records_Cache();
    }
}