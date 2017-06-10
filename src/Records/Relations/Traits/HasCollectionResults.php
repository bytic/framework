<?php

namespace Nip\Records\Relations\Traits;

use Nip\Records\Collections\Associated as AssociatedCollection;

/**
 * Trait HasCollectionResults
 * @package Nip\Records\Relations\Traits
 *
 * @method string getParam($name)
 */
trait HasCollectionResults
{

    /**
     * @return AssociatedCollection
     */
    public function newCollection()
    {
        $class = $this->getCollectionClass();
        $collection = new $class();
        /** @var AssociatedCollection $collection */
        $collection->initFromRelation($this);

        return $collection;
    }

    /**
     * @return mixed|string
     */
    public function getCollectionClass()
    {
        $collection = $this->getParam('collection');
        if ($collection) {
            return $collection;
        }

        return AssociatedCollection::class;
    }

}