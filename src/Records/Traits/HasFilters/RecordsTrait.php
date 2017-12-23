<?php

namespace Nip\Records\Traits\HasFilters;

use Nip\Database\Query\Select as SelectQuery;
use Nip\Records\Filters\FilterManager;
use Nip\Request;

/**
 * Class RecordsTrait
 * @package Nip\Records\Traits\HasFilters
 */
trait RecordsTrait
{
    protected $filterManager = null;

    /**
     * @param Request|array $request
     * @return mixed
     */
    public function requestFilters($request)
    {
        $request = $request instanceof Request ? $request : new Request($request);
        $this->getFilterManager()->setRequest($request);

        return $this->getFilterManager()->getFiltersArray();
    }

    /**
     * @return FilterManager
     */
    public function getFilterManager()
    {
        if ($this->filterManager === null) {
            $this->initFilterManager();
        }

        return $this->filterManager;
    }

    /**
     * @param FilterManager $filterManager
     */
    public function setFilterManager($filterManager)
    {
        $this->filterManager = $filterManager;
    }

    public function initFilterManager()
    {
        $class = $this->getFilterManagerClass();
        /** @var FilterManager $manager */
        $manager = new $class();
        $manager->setRecordManager($this);
        $manager->setRequest($this->getRequest());
        $this->setFilterManager($manager);
        $this->initFilters();
    }

    /**
     * @return string
     */
    public function getFilterManagerClass()
    {
        return $this->generateFilterManagerClass();
    }

    /**
     * @return mixed
     */
    protected function generateFilterManagerClass()
    {
        $base = $this->getNamespacePath();
        $namespaceClass = $base . '\Filters\FilterManager';
        if (class_exists($namespaceClass)) {
            return $namespaceClass;
        }

        return $this->generateFilterManagerDefaultClass();
    }

    /**
     * @return mixed
     */
    protected function generateFilterManagerDefaultClass()
    {
        return FilterManager::class;
    }

    /**
     *
     */
    public function initFilters()
    {
        $this->getFilterManager()->init();
    }

    /**
     * @param $query
     * @return SelectQuery
     * @internal param array $filters
     */
    public function filter($query)
    {
        $query = $this->filterQuery($query);

        return $query;
    }

    /**
     * @param SelectQuery $query
     * @return SelectQuery
     */
    public function filterQuery($query)
    {
        return $this->getFilterManager()->filterQuery($query);
    }
}
