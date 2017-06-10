<?php

namespace Nip\Records\Traits\AbstractTrait;

use Nip\Database\Query\AbstractQuery;
use Nip\Database\Query\Select;
use Nip\Records\Record;

/**
 * Class RecordsTrait
 * @package ByTIC\Common\Records\Traits\AbstractTrait
 */
trait RecordsTrait
{

    /**
     * @param array $data [optional]
     * @return Record
     */
    abstract public function getNew($data = []);

    /**
     * @param string $type
     * @return AbstractQuery|Select
     */
    abstract public function newQuery($type = 'select');

    /**
     * @param $query
     * @param array $params
     * @return RecordTrait
     */
    abstract public function findOneByQuery($query, $params = []);

    /**
     * @return string
     */
    abstract public function getController();

    /**
     * @return string
     */
    abstract public function getModel();

    /**
     * @return string
     */
    abstract public function getTable();

    /**
     * @return string
     */
    abstract public function getRootNamespace();

    /**
     * @return string
     */
    abstract public function getModelNamespace();

    /**
     * @param $name
     * @param array $params
     * @return mixed
     */
    abstract public function hasMany($name, $params = []);
}
