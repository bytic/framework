<?php

namespace Nip\Database\Adapters\Profiler;

class Profiler extends \Nip_Profiler
{
    public $filterTypes = null;

    public function newProfile($id)
    {
        return new QueryProfile($id);
    }

    protected function applyFilters($profile)
    {
        if (parent::applyFilters($profile)) {
            return $this->secondsFilter($profile);
        }
    }

    public function typeFilter($profile)
    {
        if (is_array($this->filterTypes) && in_array($profile->type, $this->filterTypes)) {
            $this->deleteProfile($profile);

            return false;
        }

        return true;
    }

    public function setFilterQueryType($queryTypes = null)
    {
        $this->filterTypes = $queryTypes;

        return $this;
    }
}
