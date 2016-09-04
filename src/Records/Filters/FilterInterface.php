<?php

namespace Nip\Records\Filters;

interface FilterInterface
{

    public function getName();

    public function getValue();

    public function getManager();

    public function setManager($field);

}