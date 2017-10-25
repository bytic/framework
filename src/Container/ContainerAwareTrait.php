<?php

namespace Nip\Container;

/**
 * Class ContainerAwareTrait
 * @package Nip\Container
 */
trait ContainerAwareTrait
{
    /**
     * @var \Nip\Container\ContainerInterface
     */
    protected $container;

    /**
     * Get the container.
     *
     * @return \Nip\Container\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Set a container.
     *
     * @param  \Nip\Container\ContainerInterface $container
     * @return $this
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasContainer()
    {
        return $this->container instanceof ContainerInterface;
    }
}
