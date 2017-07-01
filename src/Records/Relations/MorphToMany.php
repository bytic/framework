<?php

namespace Nip\Records\Relations;

use Nip\Database\Query\AbstractQuery;
use Nip\Database\Query\Delete as DeleteQuery;
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

    /** @noinspection PhpMissingParentCallCommonInspection
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
     * @param AbstractQuery $query
     * @return AbstractQuery
     */
    public function populateQuerySpecific(AbstractQuery $query)
    {
        if ($this->isInverse()) {
            return parent::populateQuerySpecific($query);
        }
        $pk1 = $this->getManager()->getPrimaryKey();

        $query->where("`{$this->getTable()}`.`pivotal_id` = ?", $this->getItem()->{$pk1});

        return $query;
    }

    /**
     * @inheritdoc
     */
    protected function formatAttachData($record)
    {
        $data = parent::formatAttachData($record);
        $data[$this->getMorphKey()] = $this->getMorphType();
        return $data;
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
        return $this->isInverse() ? $this->getWith()->getTable() : $this->getManager()->getTable();
    }

    /**
     * @param DeleteQuery $query
     * @param $records
     */
    protected function queryDetachRecords($query, $records)
    {
        parent::queryDetachRecords($query, $records);
        $query->where("`{$this->getMorphKey()}` = ?", $this->getMorphType());
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
     * @return mixed
     */
    protected function getPivotFK()
    {
        if ($this->isInverse()) {
            return 'pivotal_id';
        }
        return parent::getPivotFK();
    }
}
