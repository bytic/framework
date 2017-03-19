<?php

namespace Nip\Records\Traits\HasFilters;

use Nip\AutoLoader\Loaders\Psr4Class;
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
        if ($this->isNamespaced()) {
            $base = $this->getNamespacePath();
            $namespaceClass = $base . '\Filters\FilterManager';
            /** @var Psr4Class $loader */
            $loader = app('autoloader')->getPsr4ClassLoader();
            $loader->load($namespaceClass);
            if ($loader->isLoaded($namespaceClass)) {
                return $namespaceClass;
            }
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
     * @return mixed
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
