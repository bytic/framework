<?php

namespace Nip\Records\Filters\Column;

use Nip\Records\Filters\FilterInterface as InterfaceAbstract;

interface FilterInterface extends InterfaceAbstract
{
    public function setField($name);

    public function getField();
}
