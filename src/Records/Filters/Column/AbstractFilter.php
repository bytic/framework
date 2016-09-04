<?php

namespace Nip\Records\Filters\Column;

class AbstractFilter extends \Nip\Records\Filters\AbstractFilter implements FilterInterface
{

    protected $field;

    public function initName()
    {
        $this->setName($this->getField());
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param mixed $field
     * @return self
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }

}