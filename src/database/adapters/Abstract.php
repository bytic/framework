<?php

abstract class Nip_DB_Adapters_Abstract
{

    /**
     * Executes SQL query
     *
     * @param string $sql
     * @return result ID
     */
    public function execute($sql)
    {
        $queryId = Nip_DB_Profiler::instance()->start($sql);

        $query = $this->query($sql);

        Nip_DB_Profiler::instance()->end($queryId);

        if ($query !== false) {
            if (Nip_DB_Profiler::instance()->enabled) {
                $qp = Nip_DB_Profiler::instance()->getProfile($queryId);

                $qp->info = $this->info();
                $qp->affectedRows = $this->affectedRows();
            }

            return $query;
        } else {
            trigger_error($this->error() . " [$sql]", E_USER_WARNING);
        }
    }

    abstract public function cleanData($data);

}