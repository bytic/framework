<?php

namespace Nip\Database\Adapters;

use Nip\Database\Adapters\Profiler\Profiler;

/**
 * Class AbstractAdapter.
 */
abstract class AbstractAdapter
{
    /**
     * @var null|Profiler
     */
    protected $_profiler = null;

    /**
     * Executes SQL query.
     *
     * @param string $sql
     *
     * @return mixed
     */
    public function execute($sql)
    {
        if ($this->hasProfiler()) {
            if ($profile = $this->getProfiler()->start()) {
                $profile->setName($sql);
                $profile->setAdapter($this);
            }
        }

        $result = $this->query($sql);

        if ($this->hasProfiler() && $profile !== null) {
            $this->getProfiler()->end($profile);
        }

        if ($result !== false) {
            return $result;
        } else {
            trigger_error($this->error()." [$sql]", E_USER_WARNING);
        }

        return false;
    }

    /**
     * @return bool
     */
    public function hasProfiler()
    {
        return is_object($this->_profiler);
    }

    /**
     * @return Profiler|null
     */
    public function getProfiler()
    {
        return $this->_profiler;
    }

    public function setProfiler($profiler)
    {
        $this->_profiler = $profiler;
    }

    abstract public function query($sql);

    abstract public function error();

    public function newProfiler()
    {
        $profiler = new Profiler();

        return $profiler;
    }

    abstract public function quote($value);

    abstract public function cleanData($data);

    abstract public function connect(
        $host = false,
        $user = false,
        $password = false,
        $database = false,
        $newLink = false
    );

    abstract public function describeTable($table);

    abstract public function disconnect();

    abstract public function lastInsertID();

    abstract public function affectedRows();
}
