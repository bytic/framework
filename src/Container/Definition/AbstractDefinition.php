<?php

namespace Nip\Container\Definition;

/**
 * Class AbstractDefinition.
 */
abstract class AbstractDefinition implements DefinitionInterface
{
    /**
     * @var string
     */
    protected $alias;
    /**
     * @var mixed
     */
    protected $concrete;

    /**
     * @var bool
     */
    protected $shared = false;

    /**
     * Constructor.
     *
     * @param string $alias
     * @param mixed  $concrete
     */
    public function __construct($alias, $concrete)
    {
        $this->alias = $alias;
        $this->concrete = $concrete;
    }

    /**
     * Whether this service is shared.
     *
     * @return bool
     */
    public function isShared()
    {
        return $this->shared;
    }

    /**
     * Sets if the service must be shared or not.
     *
     * @param bool $shared Whether the service must be shared or not
     *
     * @return $this The current instance
     */
    public function setShared($shared)
    {
        $this->shared = (bool) $shared;

        return $this;
    }
}
