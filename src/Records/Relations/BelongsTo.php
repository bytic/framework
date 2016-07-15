<?php

namespace Nip\Records\Relations;

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

}