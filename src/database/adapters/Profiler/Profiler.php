<?php

namespace Nip\Database\Adapters\Profiler;

class Profiler extends \Nip_Profiler
{

    public $filterTypes = null;


    public function start($queryText = false)
    {
        if (!$this->checkEnabled()) {
            return;
        }

        // make sure we have a query type
        switch (strtolower(substr($queryText, 0, 6))) {
            case 'insert':
                $queryType = 'INSERT';
                break;

            case 'update':
                $queryType = 'UPDATE';
                break;

            case 'delete':
                $queryType = 'DELETE';
                break;

            case 'select':
                $queryType = 'SELECT';
                break;

            default:
                $queryType = 'QUERY';
                break;
        }

        $this->profiles[] = new QueryProfile($queryText, $queryType);

        $profileID = $this->lastProcessID();
        $this->addRunningProces($profileID);
        return $profileID;
    }


    public function end($profileID = false)
    {
        if (!$this->checkEnabled()) {
            return;
        }

        if ($profileID == false) {
            $profileID = $this->getLastRunningProces();
        }

        $profile = $this->endProfile($profileID);
        if (is_object($profile)) {
            $this->secondsFilter($profile);
            $this->typeFilter($profile);
        }
        return;
    }


    public function typeFilter($profile)
    {
        if (is_array($this->filterTypes) && in_array($profile->type, $this->filterTypes)) {
            $this->deleteProfile($profile);
            return;
        }
    }


    public function setFilterQueryType($queryTypes = null)
    {
        $this->filterTypes = $queryTypes;

        return $this;
    }
}