<?php

namespace Nip\Html\Head\Tags;

/**
 * Class Meta.
 */
class Meta extends AbstractTag
{
    protected $element = 'meta';

    /**
     * @param $value
     *
     * @return bool|$this
     */
    public function setName($value)
    {
        return $this->setAttribute('name', $value);
    }

    /**
     * @param $value
     *
     * @return bool|$this
     */
    public function setContent($value)
    {
        return $this->setAttribute('content', $value);
    }

    protected function initValidAttributes()
    {
        parent::initValidAttributes();
        $this->addValidAttributes('name', 'content');
    }
}
