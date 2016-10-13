<?php

namespace Nip\Container;

/**
 * Class ContainerAwareTrait
 * @package Nip\Container
 */
trait ContainerAwareTrait
{
    /**
     * @var \Nip\Container\ContainerInterface|null
     */
    protected $container = null;

    /**
     * Get the container.
     *
     * @return \Nip\Container\ContainerInterface
     */
    public function getContainer()
    {
        if ($this->container == null) {
            $this->initContainer();
        }

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

    public function initContainer()
    {
        $this->container = $this->newContainer();
        Container::setInstance($this->container);
    }

    /**
     * @return Container
     */
    public function newContainer()
    {
        return new Container();
    }
}
