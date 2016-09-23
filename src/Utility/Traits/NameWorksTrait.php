<?php

namespace Nip\Utility\Traits;

/**
 * Class NameWorksTrait
 * @package Nip\Utility\Traits
 */
trait NameWorksTrait
{

    /**
     * @var null|boolean
     */
    protected $className = null;

    /**
     * @var null|boolean
     */
    protected $isNamespaced = null;

    /**
     * @return bool
     */
    public function isNamespaced()
    {
        if ($this->isNamespaced === null) {
            $class = $this->getClassName();

            $this->isNamespaced = strpos($class, '\\') !== false;
        }

        return $this->isNamespaced;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        if ($this->className === null) {
            $this->setClassName(get_class($this));
        }

        return $this->className;
    }

    /**
     * @param bool|null $className
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }
}
