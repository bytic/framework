<?php

namespace Nip\Records\Relations;

use Nip\Records\Collections\Collection as RecordCollection;

/**
 * Class BelongsTo
 * @package Nip\Records\Relations
 */
class BelongsTo extends Relation
{

    /**
     * @var string
     */
    protected $type = 'belongsTo';

    /** @noinspection PhpMissingParentCallCommonInspection
     * @return string
     */
    public function generateFK()
    {
        return $this->getWith()->getPrimaryFK();
    }

    public function initResults()
    {
        $manager = $this->getWith();
        $fk = $this->getItem()->{$this->getFK()};
        $this->setResults($manager->findOne($fk));
    }

    /**
     * @param $dictionary
     * @param $collection
     * @param $record
     * @return mixed
     */
    public function getResultsFromCollectionDictionary($dictionary, $collection, $record)
    {
        $pk = $record->{$this->getFK()};
        if (isset($dictionary[$pk])) {
            return $dictionary[$pk];
        }

        return false;
    }

    /**
     * Build model dictionary keyed by the relation's foreign key.
     *
     * @param RecordCollection $collection
     * @return array
     */
    protected function buildDictionary(RecordCollection $collection)
    {
        $dictionary = [];
        $withPK = $this->getWithPK();
        foreach ($collection as $record) {
            $dictionary[$record->{$withPK}] = $record;
        }

        return $dictionary;
    }
}
