<?php

namespace Nip\Records\Filters;

use Nip\Database\Query\Select;
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
     * @var null|string
     */
    protected $requestField = null;

    /**
     * @var null|mixed
     */
    protected $value = null;

    /**
     * @var FilterManager
     */
    protected $manager;

    /**
     * @param Select $query
     */
    public function filterQuery($query)
    {
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->hasValue();
    }

    /**
     * @return bool
     */
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
     * @param string|false $value
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

    /**
     * @return string|false
     */
    public function getProcessedRequestValue()
    {
        $value = $this->getValueFromRequest();
        if ($this->isValidRequestValue($value)) {
            return $this->cleanRequestValue($value);
        }

        return false;
    }

    /**
     * @return string|false
     */
    public function getValueFromRequest()
    {
        $request = $this->getRequest();
        $name = $this->getRequestField();
        if ($name) {
            $return = $request->get($name);
            if ($return) {
                return trim($return);
            }
        }

        return false;
    }

    /**
     * @return null|string
     */
    public function getRequestField()
    {
        if ($this->requestField === null) {
            $this->initRequestField();
        }

        return $this->requestField;
    }

    /**
     * @param null|string $requestField
     */
    public function setRequestField($requestField)
    {
        $this->requestField = $requestField;
    }

    public function initRequestField()
    {
        $this->setRequestField($this->getName());
    }

    /**
     * @return null|string
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
     * @param string|false $value
     * @return bool
     */
    public function isValidRequestValue($value)
    {
        return !empty($value);
    }

    /**
     * @param string|false $value
     * @return string
     */
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
