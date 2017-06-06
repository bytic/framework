<?php

namespace Nip\Records\AbstractModels;

use Nip\HelperBroker;
use Nip\Logger\Exception;
use Nip\Records\Relations\HasMany;
use Nip\Records\Relations\Relation;
use Nip\Utility\Traits\NameWorksTrait;

/**
 * Class Row
 * @package Nip\Records\_Abstract
 *
 * @method \Nip_Helper_Url URL()
 */
abstract class Record extends \Nip_Object
{
    use NameWorksTrait;

    protected $_name = null;
    protected $_manager = null;

    /**
     * @var string
     */
    protected $managerName = null;

    protected $_dbData = [];
    protected $_helpers = [];


    /**
     * The loaded relationships for the model.
     * @var array
     */
    protected $relations = [];


    /**
     * Overloads Ucfirst() helper
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {

        if (substr($name, 0, 3) == "get") {
            $relation = $this->getRelation(substr($name, 3));
            if ($relation) {
                return $relation->getResults();
            }
        }

        if ($name === ucfirst($name)) {
            return $this->getHelper($name);
        }

        trigger_error("Call to undefined method $name", E_USER_ERROR);
        return null;
    }

    /**
     * @param $relationName
     * @return Relation|HasMany|null
     */
    public function getRelation($relationName)
    {
        if (!$this->hasRelation($relationName)) {
            $this->initRelation($relationName);
        }

        return $this->relations[$relationName];
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasRelation($key)
    {
        return array_key_exists($key, $this->relations);
    }

    /**
     * @param $relationName
     */
    public function initRelation($relationName)
    {
        if (!$this->getManager()->hasRelation($relationName)) {
            return;
        }
        $this->relations[$relationName] = $this->newRelation($relationName);
    }

    /**
     * @return \Nip\Records\RecordManager
     */
    public function getManager()
    {
        if ($this->_manager == null) {
            $this->initManager();
        }

        return $this->_manager;
    }

    /**
     * @param RecordManager $manager
     */
    public function setManager($manager)
    {
        $this->_manager = $manager;
    }

    protected function initManager()
    {
        $class = $this->getManagerName();
        $manager = $this->getManagerInstance($class);
        $this->setManager($manager);
    }

    /**
     * @return null
     */
    public function getManagerName()
    {
        if ($this->managerName === null) {
            $this->initManagerName();
        }

        return $this->managerName;
    }

    /**
     * @param string $managerName
     */
    public function setManagerName($managerName)
    {
        $this->managerName = $managerName;
    }

    protected function initManagerName()
    {
        $this->setManagerName($this->inflectManagerName());
    }

    /**
     * @return string
     */
    protected function inflectManagerName()
    {
        return ucfirst(inflector()->pluralize($this->getClassName()));
    }

    /**
     * @param string $class
     * @return mixed
     * @throws Exception
     */
    protected function getManagerInstance($class)
    {
        if (class_exists($class)) {
            return call_user_func([$class, 'instance']);
        }
        throw new Exception('invalid manager name [' . $class . ']');
    }

    /**
     * @param string $relationName
     * @return Relation|null
     */
    public function newRelation($relationName)
    {
        $relation = clone $this->getManager()->getRelation($relationName);
        $relation->setItem($this);

        return $relation;
    }

    /**
     * @param $name
     * @return \Nip\Helpers\AbstractHelper
     */
    public function getHelper($name)
    {
        return HelperBroker::get($name);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        if ($this->_name == null) {
            $this->_name = inflector()->unclassify(get_class($this));
        }
        return $this->_name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * @param bool|array $data
     */
    public function writeDBData($data = false)
    {
        foreach ($data as $key => $value) {
            $this->_dbData[$key] = $value;
        }
    }

    /**
     * @return array
     */
    public function getDBData()
    {
        return $this->_dbData;
    }

    /**
     * @return mixed
     */
    public function getPrimaryKey()
    {
        $pk = $this->getManager()->getPrimaryKey();

        return $this->{$pk};
    }

    /**
     * @return bool
     */
    public function insert()
    {
        $pk = $this->getManager()->getPrimaryKey();
        $lastId = $this->getManager()->insert($this);
        if ($pk == 'id') {
            $this->{$pk} = $lastId;
        }

        return $lastId > 0;
    }

    /**
     * @return bool|\Nip\Database\Result
     */
    public function update()
    {
        $return = $this->getManager()->update($this);
        return $return;
    }

    public function save()
    {
        $this->getManager()->save($this);
    }

    public function saveRecord()
    {
        $this->getManager()->save($this);
    }

    public function saveRelations()
    {
        $relations = $this->getRelations();
        foreach ($relations as $relation) {
            /** @var Relation $relation */
            $relation->save();
        }
    }

    /**
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }

    public function delete()
    {
        $this->getManager()->delete($this);
    }

    /**
     * @return bool
     */
    public function isInDB()
    {
        $pk = $this->getManager()->getPrimaryKey();
        return $this->{$pk} > 0;
    }

    /**
     * @return bool|false|Record
     */
    public function exists()
    {
        return $this->getManager()->exists($this);
    }

    /**
     * @return string
     */
    public function toJSON()
    {
        return json_encode($this->toArray());
    }

    /**
     * @return mixed
     */
    public function toArray()
    {
        $vars = get_object_vars($this);
        return $vars['_data'];
    }

    /**
     * @return mixed
     */
    public function toApiArray()
    {
        $data = $this->toArray();
        return $data;
    }

    /**
     * @return Record
     */
    public function getCloneWithRelations()
    {
        $item = $this->getClone();
        $item->cloneRelations($this);

        return $item;
    }

    /**
     * @return Record
     */
    public function getClone()
    {
        $clone = $this->getManager()->getNew();
        $clone->updateDataFromRecord($this);

        unset($clone->{$this->getManager()->getPrimaryKey()}, $clone->created);

        return $clone;
    }

    /**
     * @param self $record
     */
    public function updateDataFromRecord($record)
    {
        $data = $record->toArray();
        $this->writeData($data);

        unset($this->{$this->getManager()->getPrimaryKey()}, $this->created);
    }

    /**
     * @param bool|array $data
     */
    public function writeData($data = false)
    {
        foreach ($data as $key => $value) {
            $this->__set($key, $value);
        }
    }

    /**
     * Clone the relations records from a sibling
     * @param self $from
     * @return self
     */
    public function cloneRelations($from)
    {
        return $this->getManager()->cloneRelations($from, $this);
    }

    /**
     * @return \Nip\Request
     */
    protected function getRequest()
    {
        return $this->getManager()->getRequest();
    }
}
