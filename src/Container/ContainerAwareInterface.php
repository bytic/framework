<?php

namespace Nip\Container;

/**
 * Interface ContainerAwareInterface
 * @package Nip\Container
 */
interface ContainerAwareInterface
{
    /**
     * Set a container
     *
     * @param \Nip\Container\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container);

    /**
     * Get the container
     *
     * @return \Nip\Container\ContainerInterface
     */
    public function getContainer();
}
