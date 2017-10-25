<?php

namespace Nip\Container;

use ArrayAccess;
use League\Container\ServiceProvider\ServiceProviderInterface;
use Psr\Container\ContainerInterface as PsrInterface;

/**
 * Interface ContainerInterface
 * @package Nip\Container
 */
interface ContainerInterface extends PsrInterface, ArrayAccess
{
    /**
     * Add an item to the container.
     *
     * @param  string $alias
     * @param  mixed|null $concrete
     * @param  boolean $share
     */
    public function set($alias, $concrete = null, $share = false);

    /**
     * Convenience method to add an item to the container as a shared item.
     *
     * @param  string $alias
     * @param  mixed|null $concrete
     */
    public function share($alias, $concrete = null);

    /**
     * Convenience method to add an item to the container as a shared item.
     *
     * @param $abstract
     * @param  string $alias
     * @return
     */
    public function alias($abstract, $alias);


    /**
     * @param $alias
     */
    public function remove($alias);

    /**
     * Add a service provider to the container.
     *
     * @param  string|ServiceProviderInterface $provider
     * @return void
     */
    public function addServiceProvider($provider);
}
