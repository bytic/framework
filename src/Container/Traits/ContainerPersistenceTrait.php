<?php

namespace Nip\Container\Traits;

use Nip\Container\ContainerInterface;

/**
 * Class ContainerPersistenceTrait
 * @package Nip\Container
 */
trait ContainerPersistenceTrait
{
    /**
     * The current globally available container (if any).
     *
     * @var static
     */
    protected static $instance;

    /**
     * Set the globally available instance of the container.
     *
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * Set the shared instance of the container.
     *
     * @param  ContainerInterface|null $container
     * @return null|ContainerInterface
     */
    public static function setInstance(ContainerInterface $container = null)
    {
        return static::$instance = $container;
    }
}
