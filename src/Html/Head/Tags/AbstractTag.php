<?php

namespace Nip\Html\Head\Tags;

/**
 * Class AbstractEntity.
 */
abstract class AbstractTag
{
    protected $element = null;

    protected $attributes = [];

    /**
     * @var null|array
     */
    protected $validAttributes = null;

    /**
     * AbstractTag constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @param $name
     * @param string $value
     *
     * @return bool|AbstractTag
     */
    public function setAttribute($name, $value)
    {
        if (!$this->isValidAttribute($name)) {
            return false;
        }
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function isValidAttribute($name)
    {
        return in_array($name, $this->getValidAttributes());
    }

    /**
     * @return array
     */
    public function getValidAttributes()
    {
        if ($this->validAttributes == null) {
            $this->initValidAttributes();
        }

        return $this->validAttributes;
    }

    /**
     * @param array $validAttributes
     */
    public function setValidAttributes($validAttributes)
    {
        $this->validAttributes = $validAttributes;
    }

    protected function initValidAttributes()
    {
        $this->validAttributes = [];
    }

    public function addValidAttributes()
    {
        $args = func_get_args();
        foreach ($args as $arg) {
            $this->addValidAttribute($arg);
        }
    }

    /**
     * @param $name
     */
    public function addValidAttribute($name)
    {
        $this->validAttributes[] = $name;
    }

    /**
     * @return string
     */
    public function render()
    {
        $return = '<';
        $return .= $this->element;
        $return .= $this->renderAttributes();
        $return .= '>';

        return $return;
    }

    /**
     * @return string
     */
    protected function renderAttributes()
    {
        $return = '';
        foreach ($this->attributes as $name => $value) {
            $return .= ' ' . $name . '="' . $value . '"';
        }

        return $return;
    }
}
