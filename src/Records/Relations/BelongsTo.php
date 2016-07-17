<?php

namespace Nip\Records\Relations;

use Nip_RecordCollection as RecordCollection;

class BelongsTo extends Relation
{

    public function initFK()
    {
        $name = $this->getWith()->getPrimaryFK();
        $this->setFK($name);
    }

    public function initResults()
    {
        $manager = $this->getWith();
        $fk = $this->getItem()->{$this->getFK()};
        $this->setResults($manager->findOne($fk));
    }

    function getResultsFromCollectionDictionary($dictionary, $collection, $record)
    {
        $pk = $record->{$this->getFK()};
        if ($dictionary[$pk]) {
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