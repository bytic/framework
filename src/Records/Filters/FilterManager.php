<?php

namespace Nip\Records\Filters;

use Nip\Database\Query\Select as SelectQuery;
use Nip\Records\AbstractModels\RecordManager;
use Nip\Records\Filters\Column\AbstractFilter as AbstractColumnFilter;
use Nip\Utility\Traits\HasRequestTrait;

/**
 * Class FilterManager
 * @package Nip\Records\Filters
 */
class FilterManager
{
    use HasRequestTrait;

    /**
     * @var AbstractFilter[]|AbstractColumnFilter[]
     */
    protected $filters = [];

    protected $filtersArray = null;

    /**
     * @var null|RecordManager
     */
    protected $recordManager = null;

    /**
     * @return null
     */
    public function getFiltersArray()
    {
        if ($this->filtersArray === null) {
            $this->initFiltersArray();
        }

        return $this->filtersArray;
    }

    /**
     * @param null $filtersArray
     */
    public function setFiltersArray($filtersArray)
    {
        $this->filtersArray = $filtersArray;
    }

    public function initFiltersArray()
    {
        $filtersArray = array();
        $filters = $this->getFilters();
        $request = $this->getRequest();
        foreach ($filters as $filter) {
            $filter->setRequest($request);
            if ($filter->isActive()) {
                $filtersArray[$filter->getName()] = $filter->getValue();
            }
        }
        $this->setFiltersArray($filtersArray);
    }

    /**
     * @return AbstractFilter[]|AbstractColumnFilter[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param SelectQuery $query
     */
    public function filterQuery($query)
    {
        $filters = $this->getFilters();
        foreach ($filters as $filter) {
            if ($filter->isActive()) {
                $filter->filterQuery($query);
            }
        }
    }

    /**
     * @param mixed $type
     * @return AbstractFilter|AbstractColumnFilter ;
     */
    public function newFilter($type)
    {
        $class = $this->getFilterClass($type);
        $filter = new $class;

        return $filter;
    }

    public function getFilterClass($type)
    {
        return '\Nip\Records\Filters\\'.$type;
    }

    /**
     * @param AbstractFilter|AbstractColumnFilter $filter
     */
    public function addFilter($filter)
    {
        $this->prepareFilter($filter);
        $this->filters[] = $filter;
    }

    /**
     * @param AbstractFilter|AbstractColumnFilter $filter
     */
    public function prepareFilter($filter)
    {
        $filter->setManager($this);
    }

    /**
     * @return null|RecordManager
     */
    public function getRecordManager()
    {
        return $this->recordManager;
    }

    /**
     * @param RecordManager $recordManager
     */
    public function setRecordManager($recordManager)
    {
        $this->recordManager = $recordManager;
    }

}