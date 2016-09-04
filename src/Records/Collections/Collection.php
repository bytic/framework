<?php

namespace Nip\Records\Collections;

use Nip\HelperBroker;
use Nip\Records\AbstractModels\RecordManager as Records;
use Nip\Records\Record as Record;

class Collection extends \Nip_Collection
{
    protected $_indexKey = false;

    /**
     * @var Records
     */
    protected $_manager = null;


    public function loadRelations($relations)
    {
        if (is_string($relations)) {
            $relations = func_get_args();
        }

        foreach ($relations as $relation) {
            $this->loadRelation($relation);
        }
    }

    public function loadRelation($name)
    {
        $relation = $this->getRelation($name);
        $results = $relation->getEagerResults($this);
        $relation->match($this, $results);
        return $results;
    }

    public function getRelation($name)
    {
        return $this->getManager()->getRelation($name);
    }

    /**
     * @return Records
     */
    public function getManager()
    {
        if ($this->_manager == null) {
            $this->initManager();
        }

        return $this->_manager;
    }

    /**
     * @param Records $manager
     * @return $this
     */
    public function setManager(Records $manager)
    {
        $this->_manager = $manager;

        return $this;
    }

    public function initManager()
    {
        $manager = $this->rewind()->getManager();
        $this->setManager($manager);
    }

    public function toJSON()
    {
        $return = array();
        foreach ($this as $item) {
            $return = $item->toArray();
        }

        return json_encode($return);
    }

    public function save()
    {
        if (count($this) > 0) {
            foreach ($this as $item) {
                $item->save();
            }
        }
    }

    /**
     * @param Record $record
     * @param null $index
     */
    public function add(Record $record, $index = null)
    {
        $index = $this->getRecordKey($record, $index);
        return $this->offsetSet($index, $record);
    }

    public function getRecordKey(Record $record, $index = null)
    {
        if ($index) {
            $index = $record->$index;
        } else {
            $index = $this->getIndexKey();
            $index = $index ? $record->$index : $record->getPrimaryKey();
            if (!$index) {
                $index = null;
            }
        }
        return $index;
    }

    public function getIndexKey()
    {
        return $this->_indexKey;
    }

    public function setIndexKey($key)
    {
        return $this->_indexKey = $key;
    }

    public function has(Record $record)
    {
        $index = $this->getRecordKey($record);

        return $this->offsetExists($index) && $this->offsetGet($index) == $record;
    }

    public function remove($record)
    {
        foreach ($this as $key => $item) {
            if ($item == $record) {
                unset($this[$key]);
            }
        }
    }

    /**
     * When $each is true, each record gets it's delete() method called.
     * Otherwise, a delete query is built for the entire collection
     *
     * @param bool $each
     * @return $this
     */
    public function delete($each = false)
    {
        if (count($this) > 0) {
            if ($each) {
                foreach ($this as $item) {
                    $item->delete();
                }
            } else {
                $pk = $this->getManager()->getPrimaryKey();
                $pk_list = HelperBroker::get('Arrays')->pluck($this, $pk);

                $query = $this->getManager()->newQuery("delete");
                $query->where("$pk IN ?", $pk_list);
                $query->execute();
            }

            $this->clear();
        }

        return $this;
    }
}