<?php

namespace Nip\Records\AbstractModels;

use Nip\Collections\Registry;
use Nip\Database\Query\Insert as InsertQuery;
use Nip\HelperBroker;
use Nip\Records\Collections\Collection as RecordCollection;
use Nip\Records\Relations\Relation;
use Nip\Records\Traits\ActiveRecords\ActiveRecordsTrait;
use Nip\Utility\Traits\NameWorksTrait;

/**
 * Class Table
 * @package Nip\Records\_Abstract
 *
 * @method \Nip_Helper_Url Url()
 */
abstract class RecordManager
{
    use NameWorksTrait;
    use ActiveRecordsTrait;

    /**
     * Collection class for current record manager
     *
     * @var string
     */
    protected $collectionClass = null;

    protected $helpers = [];

    /**
     * @var null|string
     */
    protected $urlPK = null;

    /**
     * Model class name
     * @var null|string
     */
    protected $model = null;

    /**
     * @var null|string
     */
    protected $controller = null;

    /**
     * @var null|string
     */
    protected $modelNamespacePath = null;

    protected $registry = null;

    /**
     * The loaded relationships for the model table.
     * @var array
     */
    protected $relations = null;

    protected $relationTypes = ['belongsTo', 'hasMany', 'hasAndBelongsToMany'];

    /**
     * @return string
     */
    public function getModelNamespace()
    {
        return $this->getRootNamespace().$this->getModelNamespacePath();
    }

    /**
     * @return string
     */
    public function getRootNamespace()
    {
        return app('app')->getRootNamespace() . 'Models\\';
    }

    /**
     * @return string
     */
    public function getModelNamespacePath()
    {
        if ($this->modelNamespacePath == null) {
            $this->initModelNamespacePath();
        }

        return $this->modelNamespacePath;
    }

    public function initModelNamespacePath()
    {
        if ($this->isNamespaced()) {
            $path = $this->generateModelNamespacePathFromClassName().'\\';
        } else {
            $controller = $this->generateControllerGeneric();
            $path = inflector()->classify($controller).'\\';
        }
        $this->modelNamespacePath = $path;
    }

    /**
     * @return string
     */
    protected function generateModelNamespacePathFromClassName()
    {
        $className = $this->getClassName();
        $rootNamespace = $this->getRootNamespace();
        $path = str_replace($rootNamespace, '', $className);

        $nsParts = explode('\\', $path);
        array_pop($nsParts);

        return implode($nsParts, '\\');
    }

    /**
     * @return string
     */
    protected function generateControllerGeneric()
    {
        $class = $this->getClassName();

        return inflector()->unclassify($class);
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
        $return = $this->isCallDatabaseOperation($name, $arguments);
        if ($return !== null) {
            return $return;
        }

        /** @noinspection PhpAssignmentInConditionInspection */
        if ($return = $this->isCallUrl($name, $arguments)) {
            return $return;
        }

        if ($name === ucfirst($name)) {
            return $this->getHelper($name);
        }

        trigger_error("Call to undefined method $name", E_USER_ERROR);

        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool
     */
    protected function isCallUrl($name, $arguments)
    {
        if (substr($name, 0, 3) == "get" && substr($name, -3) == "URL") {
            $action = substr($name, 3, -3);
            $params = isset($arguments[0]) ? $arguments[0] : [];
            $module = isset($arguments[1]) ? $arguments[1] : null;

            return $this->compileURL($action, $params, $module);
        }

        return false;
    }

    /**
     * @param $action
     * @param array $params
     * @param null $module
     * @return mixed
     */
    public function compileURL($action, $params = [], $module = null)
    {
        $controller = $this->getController();

        if (substr($action, 0, 5) == 'Async') {
            $controller = 'async-' . $controller;
            $action = substr($action, 5);
        }

        if (substr($action, 0, 5) == 'Modal') {
            $controller = 'modal-' . $controller;
            $action = substr($action, 5);
        }

        $params['action'] = (!empty($action)) ? $action : 'index';
        $params['controller'] = $controller;

        $params['action'] = inflector()->unclassify($params['action']);
        $params['action'] = ($params['action'] == 'index') ? false : $params['action'];

        $params['controller'] = $controller ? $controller : $this->getController();
        $params['module'] = $module ? $module : request()->getModuleName();

        $routeName = $params['module'] . '.' . $params['controller'] . '.' . $params['action'];
        if ($this->Url()->getRouter()->hasRoute($routeName)) {
            unset($params['module'], $params['controller'], $params['action']);
        } else {
            $routeName = $params['module'] . '.default';
        }

        return $this->Url()->assemble($routeName, $params);
    }

    /**
     * @return string
     */
    public function getController()
    {
        if ($this->controller === null) {
            $this->initController();
        }

        return $this->controller;
    }

    /**
     * @param null|string $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    protected function initController()
    {
        if ($this->isNamespaced()) {
            $controller = $this->generateControllerNamespaced();
        } else {
            $controller = $this->generateControllerGeneric();
        }
        $this->setController($controller);
    }

    /**
     * @return string
     */
    protected function generateControllerNamespaced()
    {
        $class = $this->getModelNamespacePath();
        $class = trim($class, '\\');

        return inflector()->unclassify($class);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getHelper($name)
    {
        return HelperBroker::get($name);
    }

    /**
     * When searching by primary key, look for items in current registry before
     * fetching them from the database
     *
     * @param array $pk_list
     * @return RecordCollection
     */
    public function findByPrimary($pk_list = [])
    {
        $pk = $this->getPrimaryKey();
        $return = $this->newCollection();

        if ($pk_list) {
            $pk_list = array_unique($pk_list);
            foreach ($pk_list as $key => $value) {
                $item = $this->getRegistry()->get($value);
                if ($item) {
                    unset($pk_list[$key]);
                    $return[$item->{$pk}] = $item;
                }
            }
            if ($pk_list) {
                $query = $this->paramsToQuery();
                $query->where("$pk IN ?", $pk_list);
                $items = $this->findByQuery($query);

                if (count($items)) {
                    foreach ($items as $item) {
                        $this->getRegistry()->set($item->{$pk}, $item);
                        $return[$item->{$pk}] = $item;
                    }
                }
            }
        }

        return $return;
    }

    /**
     * @return RecordCollection
     */
    public function newCollection()
    {
        $class = $this->getCollectionClass();
        /** @var RecordCollection $collection */
        $collection = new $class();
        $collection->setManager($this);

        return $collection;
    }

    /**
     * @return string
     */
    public function getCollectionClass()
    {
        if ($this->collectionClass === null) {
            $this->initCollectionClass();
        }

        return $this->collectionClass;
    }

    /**
     * @param string $collectionClass
     */
    public function setCollectionClass($collectionClass)
    {
        $this->collectionClass = $collectionClass;
    }

    protected function initCollectionClass()
    {
        $this->setCollectionClass($this->generateCollectionClass());
    }

    /**
     * @return string
     */
    protected function generateCollectionClass()
    {
        return RecordCollection::class;
    }

    /**
     * @return \Nip\Collections\Registry
     */
    public function getRegistry()
    {
        if (!$this->registry) {
            $this->registry = new Registry();
        }

        return $this->registry;
    }

    /**
     * Factory
     *
     * @return Record
     * @param array $data [optional]
     */
    public function getNew($data = [])
    {
        $pk = $this->getPrimaryKey();
        if (is_string($pk) && isset($data[$pk]) && $this->getRegistry()->has($data[$pk])) {
            $return = $this->getRegistry()->get($data[$pk]);
            $return->writeData($data);
            $return->writeDBData($data);

            return $return;
        }

        $record = $this->getNewRecordFromDB($data);

        return $record;
    }

    /**
     * @param array $data
     * @return Record
     */
    public function getNewRecordFromDB($data = [])
    {
        $record = $this->getNewRecord($data);
        $record->writeDBData($data);

        return $record;
    }

    /**
     * @param array $data
     * @return Record
     */
    public function getNewRecord($data = [])
    {
        $model = $this->getModel();
        /** @var Record $record */
        $record = new $model();
        $record->setManager($this);
        $record->writeData($data);

        return $record;
    }

    /**
     * @return string
     */
    public function getModel()
    {
        if ($this->model == null) {
            $this->inflectModel();
        }

        return $this->model;
    }

    /**
     * @param null $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    protected function inflectModel()
    {
        $class = $this->getClassName();
        if ($this->model == null) {
            $this->model = $this->generateModelClass($class);
        }
    }

    /**
     * @param null $class
     * @return string
     */
    public function generateModelClass($class = null)
    {
        $class = $class ? $class : get_class($this);

        if (strpos($class, '\\')) {
            $nsParts = explode('\\', $class);
            $class = array_pop($nsParts);

            if ($class == 'Table') {
                $class = 'Row';
            } else {
                $class = ucfirst(inflector()->singularize($class));
            }

            return implode($nsParts, '\\').'\\'.$class;
        }

        return ucfirst(inflector()->singularize($class));
    }

    /**
     * @return \Nip\Request
     */
    public function getRequest()
    {
        return request();
    }

    public function __wakeup()
    {
        $this->initDB();
    }

    /**
     * @param Record $item
     * @return bool|false|Record
     */
    public function exists(Record $item)
    {
        $params = [];
        $params['where'] = [];

        $fields = $this->getUniqueFields();

        if (!$fields) {
            return false;
        }

        foreach ($fields as $field) {
            $params['where'][$field.'-UNQ'] = ["$field = ?", $item->{$field}];
        }

        $pk = $this->getPrimaryKey();
        if ($item->getPrimaryKey()) {
            $params['where'][] = ["$pk != ?", $item->getPrimaryKey()];
        }

        return $this->findOneByParams($params);
    }

    /**
     * @return null
     */
    public function getUniqueFields()
    {
        if ($this->uniqueFields === null) {
            $this->initUniqueFields();
        }

        return $this->uniqueFields;
    }

    /**
     * @return array|null
     */
    public function initUniqueFields()
    {
        $this->uniqueFields = [];
        $structure = $this->getTableStructure();
        foreach ($structure['indexes'] as $name => $index) {
            if ($index['unique']) {
                foreach ($index['fields'] as $field) {
                    if ($field != $this->getPrimaryKey()) {
                        $this->uniqueFields[] = $field;
                    }
                }
            }
        }

        return $this->uniqueFields;
    }

    /**
     * Finds one Record using params array
     *
     * @param array $params
     * @return Record|false
     */
    public function findOneByParams(array $params = [])
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
    public function findByParams($params = [])
    {
        $query = $this->paramsToQuery($params);

        return $this->findByQuery($query, $params);
    }

    /**
     * @return RecordCollection
     */
    public function getAll()
    {
        if (!$this->getRegistry()->has("all")) {
            $this->getRegistry()->set("all", $this->findAll());
        }

        return $this->getRegistry()->get("all");
    }

    /**
     * @return RecordCollection
     */
    public function findAll()
    {
        return $this->findByParams();
    }

    /**
     * @param int $count
     * @return mixed
     */
    public function findLast($count = 9)
    {
        return $this->findByParams([
            'limit' => $count,
        ]);
    }

    /**
     * Inserts a Record into the database
     * @param Record $model
     * @param array|bool $onDuplicate
     * @return mixed
     */
    public function insert($model, $onDuplicate = false)
    {
        $query = $this->insertQuery($model, $onDuplicate);
        $query->execute();

        return $this->getDB()->lastInsertID();
    }

    /**
     * @param $model
     * @param $onDuplicate
     * @return InsertQuery
     */
    public function insertQuery($model, $onDuplicate)
    {
        $inserts = $this->getQueryModelData($model);

        $query = $this->newInsertQuery();
        $query->data($inserts);

        if ($onDuplicate !== false) {
            $query->onDuplicate($onDuplicate);
        }

        return $query;
    }

    /**
     * @param Record $model
     * @return array
     */
    public function getQueryModelData($model)
    {
        $data = [];

        $fields = $this->getFields();
        foreach ($fields as $field) {
            if (isset($model->{$field})) {
                $data[$field] = $model->{$field};
            }
        }

        return $data;
    }

    /**
     * The name of the field used as a foreign key in other tables
     * @return string
     */
    public function getPrimaryFK()
    {
        if ($this->foreignKey == null) {
            $this->initPrimaryFK();
        }

        return $this->foreignKey;
    }

    public function initPrimaryFK()
    {
        $this->setForeignKey($this->generatePrimaryFK());
    }

    /**
     * @param null $foreignKey
     */
    public function setForeignKey($foreignKey)
    {
        $this->foreignKey = $foreignKey;
    }

    /**
     * @return string
     */
    public function generatePrimaryFK()
    {
        $singularize = inflector()->singularize($this->getController());

        return $this->getPrimaryKey()."_".inflector()->underscore($singularize);
    }

    /**
     * @param $fk
     */
    public function setPrimaryFK($fk)
    {
        $this->foreignKey = $fk;
    }

    /**
     * The name of the field used as a foreign key in other tables
     * @return string
     */
    public function getUrlPK()
    {
        if ($this->urlPK == null) {
            $this->urlPK = $this->getPrimaryKey();
        }

        return $this->urlPK;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasField($name)
    {
        $fields = $this->getFields();
        if (is_array($fields) && in_array($name, $fields)) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getFullTextFields()
    {
        $return = [];
        $structure = $this->getTableStructure();
        foreach ($structure['indexes'] as $name => $index) {
            if ($index['fulltext']) {
                $return[$name] = $index['fields'];
            }
        }

        return $return;
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
        foreach ($this->relationTypes as $type) {
            $this->initRelationsType($type);
        }
    }

    /**
     * @param string $type
     */
    protected function initRelationsType($type)
    {
        if (property_exists($this, '_' . $type)) {
            $array = $this->{'_' . $type};
            $this->initRelationsFromArray($type, $array);
        }
    }

    /**
     * @param $type
     * @param $array
     */
    public function initRelationsFromArray($type, $array)
    {
        foreach ($array as $key => $item) {
            $name = is_array($item) ? $key : $item;
            $params = is_array($item) ? $item : [];
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
     * @param string $type
     * @return string
     */
    public function getRelationClass($type)
    {
        $class = 'Nip\Records\Relations\\'.ucfirst($type);

        return $class;
    }

    /**
     * @param $name
     * @param array $params
     */
    public function belongsTo($name, $params = [])
    {
        $this->initRelation('belongsTo', $name, $params);
    }

    /**
     * @param $name
     * @param array $params
     */
    public function hasMany($name, $params = [])
    {
        $this->initRelation('hasMany', $name, $params);
    }

    /**
     * @param $name
     * @param array $params
     */
    public function HABTM($name, $params = [])
    {
        $this->initRelation('hasAndBelongsToMany', $name, $params);
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
     * @param Record $from
     * @param Record $to
     * @return Record
     */
    public function cloneRelations($from, $to)
    {
        $relations = $from->getManager()->getRelations();
        foreach ($relations as $name => $relation) {
            /** @var \Nip\Records\Relations\HasMany $relation */
            if ($relation->getType() != 'belongsTo') {
                /** @var Record[] $associatedOld */
                $associatedOld = $from->{'get'.$name}();
                if (count($associatedOld)) {
                    $associatedNew = $to->getRelation($name)->newCollection();
                    foreach ($associatedOld as $associated) {
                        $aItem = $associated->getCloneWithRelations();
                        $associatedNew[] = $aItem;
                    }
                    $to->getRelation($name)->setResults($associatedNew);
                }
            }
        }

        return $to;
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
     * Set the entire relations array on the model.
     * @param  array $relations
     * @return $this
     */
    public function setRelations(array $relations)
    {
        $this->relations = $relations;

        return $this;
    }

    /**
     * Sets model and database table from the class name
     */
    protected function inflect()
    {
        $this->initController();
    }
}
