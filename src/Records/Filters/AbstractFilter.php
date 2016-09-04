<?php

namespace Nip\Records\Filters;

use Nip\Utility\Traits\HasRequestTrait;

/**
 * Class AbstractFilter
 * @package Nip\Records\Filters
 */
class AbstractFilter implements FilterInterface
{
    use HasRequestTrait;

    /**
     * @var null|string
     */
    protected $name = null;

    /**
     * @var null|mixed
     */
    protected $value = null;

    /**
     * @var FilterManager
     */
    protected $manager;

    public function filterQuery($query)
    {
    }

    public function isActive()
    {
        return $this->hasValue();
    }

    public function hasValue()
    {
        return $this->getValue() !== false;
    }

    /**
     * @return null
     */
    public function getValue()
    {
        if ($this->value === null) {
            $this->initValue();
        }

        return $this->value;
    }

    /**
     * @param null $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    public function initValue()
    {
        $value = $this->getProcessedRequestValue();
        $this->setValue($value);
    }

    public function getProcessedRequestValue()
    {
        $value = $this->getValueFromRequest();
        if ($this->isValidRequestValue($value)) {
            return $this->cleanRequestValue($value);
        }

        return false;
    }

    public function getValueFromRequest()
    {
        $request = $this->getRequest();
        $name = $this->getName();
        if ($name) {
            $return = $request->get($name);
            if ($return) {
                return trim($return);
            }
        }

        return false;
    }

    /**
     * @return null
     */
    public function getName()
    {
        if ($this->name === null) {
            $this->initName();
        }

        return $this->name;
    }

    /**
     * @param null $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function initName()
    {
    }

    /**
     * @param $value
     * @return bool
     */
    public function isValidRequestValue($value)
    {
        return !empty($value);
    }

    public function cleanRequestValue($value)
    {
        return clean($value);
    }

    /**
     * @return null|FilterManager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param FilterManager $manager
     */
    public function setManager($manager)
    {
        $this->manager = $manager;
    }

}