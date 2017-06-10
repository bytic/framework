<?php

namespace Nip\Records\Traits\AbstractTrait;

use Nip\Records\AbstractModels\RecordManager;

/**
 * Class RecordTrait
 *
 * @package ByTIC\Common\Records\Traits\AbstractTrait
 *
 * @property int $id
 */
trait RecordTrait
{

    /**
     * @return RecordManager
     */
    abstract public function getManager();

    /**
     * @param RecordManager|RecordsTrait $manager
     * @return $this
     */
    abstract public function setManager($manager);

    /**
     * @return mixed
     */
    abstract public function update();

    /**
     * @param $data
     * @return mixed
     */
    abstract public function writeData($data = false);

    /**
     * @return array
     */
    abstract public function toArray();

    abstract public function save();

    /**
     * @return boolean
     */
    abstract public function exists();
}
