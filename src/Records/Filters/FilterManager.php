<?php

namespace Nip\Records\Filters;

use Nip\Collections\AbstractCollection;
use Nip\Collections\ArrayAccessTrait;
use Nip\Database\Query\Select as SelectQuery;
use Nip\Records\AbstractModels\RecordManager;
use Nip\Records\Filters\Column\AbstractFilter as AbstractColumnFilter;
use Nip\Records\Traits\HasFilters\RecordsTrait;
use Nip\Utility\Traits\HasRequestTrait;

/**
 * Class FilterManager
 * @package Nip\Records\Filters
 *
 * @method AbstractFilter[]|AbstractColumnFilter[] all()
 */
class FilterManager extends AbstractCollection
{
    use HasRequestTrait;
    use ArrayAccessTrait;

    /**
     * @var AbstractFilter[]|AbstractColumnFilter[]
     */
    protected $items = [];

    protected $filtersArray = null;

    /**
     * @var null|RecordManager
     */
    protected $recordManager = null;

    /**
     * Init filter Manager, init default filters
     */
    public function init()
    {
    }

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
        $filtersArray = $this->generateFiltersArray();
        $this->setFiltersArray($filtersArray);
    }

    /**
     * @return array
     */
    public function generateFiltersArray()
    {
        $filtersArray = [];
        $filters = $this->all();
        $request = $this->getRequest();
        foreach ($filters as $filter) {
            $filter->setRequest($request);
            if ($filter->isActive()) {
                $filtersArray[$filter->getName()] = $filter->getValue();
            }
        }

        return $filtersArray;
    }

    /**
     * @param SelectQuery $query
     * @return SelectQuery
     */
    public function filterQuery($query)
    {
        $filters = $this->all();
        foreach ($filters as $filter) {
            if ($filter->isActive()) {
                $filter->filterQuery($query);
            }
        }

        return $query;
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

    /**
     * @param string $type
     * @return string
     */
    public function getFilterClass($type)
    {
        return '\Nip\Records\Filters\\' . $type;
    }

    /**
     * @param AbstractFilter|AbstractColumnFilter $filter
     */
    public function addFilter($filter)
    {
        $this->prepareFilter($filter);
        $this->items[] = $filter;
    }

    /**
     * @param AbstractFilter|AbstractColumnFilter $filter
     */
    public function prepareFilter($filter)
    {
        $filter->setManager($this);
        $filter->setRequest($this->getRequest());
    }

    /**
     * @return null|RecordManager|RecordsTrait
     */
    public function getRecordManager()
    {
        return $this->recordManager;
    }

    /**
     * @param RecordManager|RecordsTrait $recordManager
     */
    public function setRecordManager($recordManager)
    {
        $this->recordManager = $recordManager;
    }
}
