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
     * @var null|array
     */
    protected $classNameParts = null;

    /**
     * @var null|boolean
     */
    protected $isNamespaced = null;

    /**
     * @return string
     */
    public function getClassName()
    {
        if ($this->className === null) {
            $this->setClassName($this->generateClassName());
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

    protected function generateClassName()
    {
        return get_class($this);
    }

    public function getNamespaceParentFolder()
    {
        if (!$this->isNamespaced()) {
            return null;
        }
        $parts = $this->getClassNameParts();
        array_pop($parts);
        return end($parts);
    }

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
     * @return array|null
     */
    public function getClassNameParts()
    {
        if ($this->classNameParts === null) {
            $this->initClassNameParts();
        }
        return $this->classNameParts;
    }

    /**
     * @param array|null $classNameParts
     */
    public function setClassNameParts($classNameParts)
    {
        $this->classNameParts = $classNameParts;
    }

    protected function initClassNameParts()
    {
        $class = $this->getClassName();
        $parts = explode('\\', $class);
        $this->setClassNameParts($parts);
    }
}
