<?php

namespace Nip\Database\Adapters;

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
            if ($profile = $this->_profiler->start()) {
                $profile->setName($sql);
                $profile->setAdapter($this);
            }
        }

        $query = $this->query($sql);

        if ($this->_profiler && $profile !== null) {
            $this->_profiler->end($profile);
        }

        if ($query !== false) {
            return $query;
        } else {
            trigger_error($this->error() . " [$sql]", E_USER_WARNING);
        }
        return;
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

    abstract public function query($sql);

    abstract public function connect($host = false, $user = false, $password = false, $database = false, $newLink = false);

    abstract public function describeTable($table);

    abstract public function disconnect();

    abstract public function lastInsertID();

    abstract public function affectedRows();

}