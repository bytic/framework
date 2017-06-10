<?php

namespace Nip\Records\Relations;

use Nip\Records\Relations\Traits\HasCollectionResults;
use Nip\Records\Relations\Traits\HasPivotTable;

/**
 * Class HasOneOrMany
 * @package Nip\Records\Relations
 */
class MorphToMany extends HasAndBelongsToMany
{
    use HasCollectionResults;
    use HasPivotTable;

    protected $type = 'morphToMany';

    /**
     * The type of the polymorphic relation.
     *
     * @var string
     */
    protected $morphType = null;

    /**
     * The class name of the morph type constraint.
     *
     * @var string
     */
    protected $morphClass;

    /**
     * Indicates if we are connecting the inverse of the relation.
     *
     * This primarily affects the morphClass constraint.
     *
     * @var bool
     */
    protected $inverse = false;

    /**
     * Get the foreign key "type" name.
     *
     * @return string
     */
    public function getMorphType()
    {
        if ($this->morphType == null) {
            $this->initMorphType();
        }
        return $this->morphType;
    }

    /**
     * @param string $morphType
     */
    public function setMorphType(string $morphType)
    {
        $this->morphType = $morphType;
    }

    protected function initMorphType()
    {
        $this->setMorphType($this->generateMorphType());
    }

    /**
     * @return string
     */
    protected function generateMorphType()
    {
        return $this->getWith()->getTable();
    }

    public function initResults()
    {
        $query = $this->getQuery();
        echo $query;
        die();
//        $items = $this->getWith()->findByQuery($query);
        $collection = $this->newCollection();
//        $this->populateCollection($collection, $items);
        $this->setResults($collection);
    }

    /**
     * Builds the name of a has-and-belongs-to-many association table
     * @return string
     */
    public function generatePivotTable()
    {
        $pivotManager = $this->getPivotManager();

        return $pivotManager->getTable() . '_pivot';
    }

    /**
     * @return \Nip\Records\AbstractModels\RecordManager
     */
    protected function getPivotManager()
    {
        return $this->isInverse() ? $this->getManager() : $this->getWith();
    }

    /**
     * @return bool
     */
    public function isInverse(): bool
    {
        return $this->inverse;
    }

    /**
     * @param bool $inverse
     */
    public function setInverse(bool $inverse)
    {
        $this->inverse = $inverse;
    }

    /**
     * @return mixed
     */
    protected function getPivotFK()
    {
        return 'pivotal_id';
    }
}