<?php

namespace Nip\Html\Head\Tags;

/**
 * Class Link.
 */
class Link extends AbstractTag
{
    protected $element = 'link';

    /**
     * @param $value
     *
     * @return bool|$this
     */
    public function setRel($value)
    {
        return $this->setAttribute('rel', $value);
    }

    /**
     * @param $value
     *
     * @return bool|$this
     */
    public function setHref($value)
    {
        return $this->setAttribute('href', $value);
    }

    /**
     * @param $value
     *
     * @return bool|$this
     */
    public function setType($value)
    {
        return $this->setAttribute('type', $value);
    }

    protected function initValidAttributes()
    {
        parent::initValidAttributes();
        $this->addValidAttributes('rel', 'href', 'type', 'color');
    }
}
