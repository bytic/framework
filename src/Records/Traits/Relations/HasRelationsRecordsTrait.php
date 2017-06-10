<?php

namespace Nip\Records\Traits\Relations;

use Nip\Records\Record;
use Nip\Records\Relations\Relation;
use Nip\Records\Traits\AbstractTrait\RecordsTrait;

/**
 * Trait HasRelationsRecordsTrait
 * @package Nip\Records\Traits\Relations
 */
trait HasRelationsRecordsTrait
{
    use RecordsTrait;

    /**
     * The loaded relationships for the model table.
     * @var Relation[]
     */
    protected $relations = null;

    protected $relationTypes = ['belongsTo', 'hasMany', 'hasAndBelongsToMany'];


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
        $class = 'Nip\Records\Relations\\' . ucfirst($type);

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
     * @param $name
     * @param array $params
     */
    public function morphToMany($name, $params = [])
    {
        $this->initRelation('morphToMany', $name, $params);
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
     * @param HasRelationsRecordTrait $from
     * @param HasRelationsRecordTrait $to
     * @return HasRelationsRecordTrait
     */
    public function cloneRelations($from, $to)
    {
        $relations = $from->getManager()->getRelations();
        foreach ($relations as $name => $relation) {
            /** @var \Nip\Records\Relations\HasMany $relation */
            if ($relation->getType() != 'belongsTo') {
                /** @var Record[] $associatedOld */
                $associatedOld = $from->{'get' . $name}();
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
}