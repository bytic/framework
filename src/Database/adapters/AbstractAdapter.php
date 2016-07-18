<?php

namespace \Nip\Database\Adapters;
use Nip\Database\Adapters\Profiler\Profiler;

abstract class AbstractAdapter
{

    /**
     * @var null|Profiler
     */
    protected $_profiler = null;

    /**
     * Executes SQL query
     *
     * @param string $sql
     * @return mixed
     */
    public function execute($sql)
    {
        if ($this->_profiler) {
            $queryProfileID = $this->_profiler->start($sql);
            $this->_profiler->getProfile($queryProfileID)->setAdapter($this);
        }

        $query = $this->query($sql);

        if ($this->_profiler) {
            $this->_profiler->end($queryProfileID);
        }

        if ($query !== false) {
            if ($this->_profiler) {
                $this->_profiler->getProfile($queryProfileID)->importInfo($this);
            }

            return $query;
        } else {
            trigger_error($this->error() . " [$sql]", E_USER_WARNING);
        }
    }

    public function setProfiler($profiler)
    {
        $this->_profiler = $profiler;
    }

    public function newProfiler()
    {
        $profiler = new Profiler();
        return $profiler;
    }

    abstract public function cleanData($data);

}