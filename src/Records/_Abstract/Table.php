<?php

namespace Nip\Records\_Abstract;

use Nip\Database\Query\_Abstract;
use Nip\Paginator;
use Nip\Records\Relations\Relation;

abstract class Table
{

    /**
     * @var \Nip_DB_Wrapper
     */
    protected $_db = null;

    protected $_collectionClass = 'Nip_RecordCollection';
    protected $_helpers = array();

    protected $_table = null;
    protected $_tableStructure = null;
    protected $_primaryKey = null;
    protected $_fields = null;
    protected $_uniqueFields = null;
    protected $_foreignKey = null;

    protected $_model = null;
    protected $_controller = null;

    protected $_registry = null;


    /**
     * The loaded relationships for the model table.
     * @var array
     */
    protected $relations = null;

    protected $_belongsTo = array();
    protected $_hasMany = array();
    protected $_hasAndBelongsToMany = array();
    protected $_relationTypes = array('belongsTo', 'hasMany', 'hasAndBelongsToMany');

    public function __construct()
    {
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
        return $this;
    }

    public function getNewRecord($data = array())
    {
        $model = $this->getModel();
        $record = new $model();
        $record->setManager($this);
        $record->writeData($data);
        return $record;
    }

    public function getNewRecordFromDB($data = array())
    {
        $record = $this->getNewRecord($data);
        $record->writeDBData($data);
        return $record;
    }

    /**
     * Sets model and database table from the class name
     */
    protected function inflect()
    {
        $this->inflectController();
    }

    protected function inflectModel()
    {
        $class = get_class($this);
        if ($this->_model == null) {
            $this->_model = $this->generateModelClass($class);
        }
    }

    public function generateModelClass($class = null)
    {
        $class = $class ? $class : get_class($this);

        if (strpos($class, '\\')) {
            $nsParts = explode('\\', $class);
            $class = array_pop($nsParts);
            if ($class == 'Table') {
                return implode($nsParts, '\\') . '\Row';
            }
        }
        return ucfirst(inflector()->singularize($class));
    }

    /**
     * @param null $model
     */
    public function setModel($model)
    {
        $this->_model = $model;
    }

    public function getModel()
    {
        if ($this->_model == null) {
            $this->inflectModel();
        }
        return $this->_model;
    }

    public function getController()
    {
        if ($this->_controller == null) {
            $this->inflectController();
        }
        return $this->_controller;
    }

    protected function inflectController()
    {
        $class = get_class($this);
        if ($this->_controller == null) {
            $this->_controller = inflector()->unclassify($class);
        }
    }

    /**
     * @return \Nip_Registry
     */
    public function getRegistry()
    {
        if (!$this->_registry) {
            $this->_registry = new \Nip_Registry();
        }
        return $this->_registry;
    }


    public function __wakeup()
    {
        $this->setUpDB();
    }

    public function getHelper($name)
    {
        return \Nip\HelperBroker::get($name);
    }


    public function exists(\Nip_Record $item)
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
     * @param string $type
     * @return _Abstract
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
     * @param \Nip_DB_Query_Abstract $query
     * @param string $class
     */
    public function associateIntoQuery(\Nip_DB_Query_Abstract $query, $class)
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

    /**
     * @return \Nip_DB_Wrapper
     */
    public function getDB()
    {
        if ($this->_db == null) {
            $this->setUpDB();
        }
        return $this->_db;
    }

    /**
     * @return \Nip_DB_Wrapper
     */
    protected function setUpDB()
    {
        $this->_db = db();
    }


    protected function getTableStructure()
    {
        if ($this->_tableStructure == null) {
            $this->initTableStructure();
        }
        return $this->_tableStructure;
    }

    protected function initTableStructure()
    {
        $this->_tableStructure = $this->getDB()->getMetadata()->describeTable($this->getTable());
    }

    /**
     * Inserts a Record into the database
     * @param Nip_Record $model
     * @param array|bool $onDuplicate
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

        $inserts = $this->getQueryModelData($model);

        $query = $this->newQuery('insert');
        $query->data($inserts);

        if ($onDuplicate !== false) {
            $query->onDuplicate($onDuplicate);
        }

        return $query;
    }

    public function getQueryModelData($model)
    {
        $data = array();

        $fields = $this->getFields();
        foreach ($fields as $field) {
            if ($model->$field) {
                $data[$field] = $model->$field;
            }
        }
        return $data;
    }

    /**
     * Updates a Record's database entry
     * @param \Nip_Record $model
     * @return bool|Nip_DB_Result
     */
    public function update(\Nip_Record $model)
    {
        $query = $this->updateQuery($model);

        if ($query) {
            return $query->execute();
        }
        return false;
    }

    /**
     * @param \Nip_Record $model
     * @return bool|Nip_DB_Query_Abstract
     */
    public function updateQuery(\Nip_Record $model)
    {
        $pk = $this->getPrimaryKey();
        if (!is_array($pk)) {
            $pk = array($pk);
        }

        $data = $this->getQueryModelData($model);

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
     * @param array $params
     * @return $this
     */
    public function deleteByParams($params = array())
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
        return $this;
    }

    /**
     * Returns paginated results
     * @param Paginator $paginator
     * @param array $params
     * @return mixed
     */
    public function paginate(Paginator $paginator, $params = array())
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
        $query->addParams($params);

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
        $query->addParams($params);

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
            $this->initPrimaryFK();
        }
        return $this->_foreignKey;
    }

    public function initPrimaryFK()
    {
        $this->_foreignKey = $this->getPrimaryKey() . "_" . inflector()->underscore($this->getModel());
    }

    public function setPrimaryFK($fk)
    {
        $this->_foreignKey = $fk;
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
            $this->initFields();
        }
        return $this->_fields;
    }

    public function initFields()
    {
        $structure = $this->getTableStructure();
        $this->_fields = array_keys($structure['fields']);
    }

    public function hasField($name)
    {
        $fields = $this->getFields();
        if (is_array($fields) && in_array($name, $fields)) {
            return true;
        }
        return false;
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
     * @return \Nip_Db_Records_Cache
     */
    public function newCacheManager()
    {
        return new \Nip_Db_Records_Cache();
    }

    /**
     * Check if the model needs to initRelations
     * @return void
     */
    protected function checkInitRelations()
    {
        if ($this->relations === null) {
            $this->initRelations();
        }
    }

    protected function initRelations()
    {
        $this->relations = [];
        foreach ($this->_relationTypes as $type) {
            $this->initRelationsType($type);
        }
    }

    protected function initRelationsType($type)
    {
        $array = $this->{'_' . $type};
        $this->initRelationsFromArray($array);
    }

    public function initRelationsFromArray($type, $array)
    {
        foreach ($array as $key => $item) {
            $name = is_array($item) ? $key : $item;
            $params = is_array($item) ? $item : array();
            $this->initRelation($type, $name, $params);
        }
    }

    /**
     * @param string $type
     * @param string $name
     * @param array $params
     * @return void
     */
    protected function initRelation($type, $name, $params)
    {
        $this->relations[$name] = $this->newRelation($type);
        $this->relations[$name]->setName($name);
        $this->relations[$name]->addParams($params);
    }

    /**
     * @param string $type
     * @return string
     */
    public function getRelationClass($type)
    {
        $class = 'Nip\Records\Relations\\' . ucfirst($type);
        return $class;
    }

    /**
     * @param string $type
     * @return \Nip\Records\Relations\Relation
     */
    public function newRelation($type)
    {
        $class = $this->getRelationClass($type);
        /** @var \Nip\Records\Relations\Relation $relation */
        $relation = new $class();
        $relation->setManager($this);
        return $relation;
    }

    /**
     * Get a specified relationship.
     * @param  string $relation
     * @return null|Relation
     */
    public function getRelation($relation)
    {
        $this->checkInitRelations();
        return $this->relations[$relation];
    }

    /**
     * Determine if the given relation is loaded.
     * @param  string $key
     * @return bool
     */
    public function hasRelation($key)
    {
        $this->checkInitRelations();
        return array_key_exists($key, $this->relations);
    }

    /**
     * Get all the loaded relations for the instance.
     * @return array
     */
    public function getRelations()
    {
        $this->checkInitRelations();
        return $this->relations;
    }

    /**
     * Set the specific relationship in the model.
     * @param  string $relation
     * @param  mixed $value
     * @return $this
     */
    public function setRelation($relation, $value)
    {
        $this->checkInitRelations();
        $this->relations[$relation] = $value;
        return $this;
    }

    /**
     * Set the entire relations array on the model.
     * @param  array $relations
     * @return $this
     */
    public function setRelations(array $relations)
    {
        $this->relations = $relations;
        return $this;
    }


}