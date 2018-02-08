<?php

namespace Nip\Database\Adapters\Profiler;

/**
 * Class Profiler
 * @package Nip\Database\Adapters\Profiler
 */
class Profiler extends \Nip_Profiler
{
    public $filterTypes = null;

    /**
     * @param $id
     * @return QueryProfile|\Nip\Profiler\Profile
     */
    public function newProfile($id)
    {
        return new QueryProfile($id);
    }

    /**
     * @param $profile
     * @return bool
     */
    protected function applyFilters($profile)
    {
        if (parent::applyFilters($profile)) {
            return $this->secondsFilter($profile);
        }
    }

    /**
     * @param $profile
     * @return bool
     */
    public function typeFilter($profile)
    {
        if (is_array($this->filterTypes) && in_array($profile->type, $this->filterTypes)) {
            $this->deleteProfile($profile);

            return false;
        }

        return true;
    }

    /**
     * @param null $queryTypes
     * @return $this
     */
    public function setFilterQueryType($queryTypes = null)
    {
        $this->filterTypes = $queryTypes;

        return $this;
    }
}
