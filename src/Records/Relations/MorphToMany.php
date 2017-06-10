<?php

namespace Nip\Records\Relations;

use Nip\Database\Query\Select as SelectQuery;
use Nip\Records\Relations\Traits\HasCollectionResults;

/**
 * Class HasOneOrMany
 * @package Nip\Records\Relations
 */
class MorphToMany extends HasAndBelongsToMany
{
    use HasCollectionResults;

    protected $type = 'morphToMany';

    /**
     * The type of the polymorphic relation.
     *
     * @var string
     */
    protected $morphType = null;

    /**
     * Indicates if we are connecting the inverse of the relation.
     *
     * This primarily affects the morphClass constraint.
     *
     * @var bool
     */
    protected $inverse = false;

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
     * @param SelectQuery $query
     */
    protected function hydrateQueryWithPivotConstraints($query)
    {
        parent::hydrateQueryWithPivotConstraints($query);
        $query->where(
            "`{$this->getTable()}`.`{$this->getMorphKey()}` = ?",
            $this->getMorphType()
        );
    }

    /**
     * Get the foreign key "type" name.
     *
     * @return string
     */
    public function getMorphKey()
    {
        return 'pivotal_type';
    }

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

    /**
     * @return mixed
     */
    protected function getPivotFK()
    {
        return 'pivotal_id';
    }
}