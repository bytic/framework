<?php

namespace Nip\Database\Adapters;

interface AdapterInterface
{
    public function execute($sql);

    public function lastInsertID();

    public function affectedRows();

    public function numRows($result);

    public function fetchArray($result);

    public function fetchAssoc($result);

    public function fetchObject($result);

    public function result($result, $row, $field);

    public function freeResults($result);

    public function describeTable($table);

    public function quote($value);

    public function cleanData($data);

    public function error();

    public function disconnect();
}
