<?php

namespace Nip\Container;

use ArrayAccess;
use Nip\Container\Definition\ClassDefinition;
use Nip\Container\Definition\DefinitionInterface;
use Nip\Container\Exception\NotFoundException;
use Nip\Container\ServiceProvider\ProviderRepository;

/**
 * Class Container.
 *
 * @inspiration https://github.com/laravel/framework/blob/2a38acf7ee2882d831a3b9a1361a710e70ffa31e/src/Illuminate/Container/Container.php
 */
class Container implements ArrayAccess, ContainerInterface
{
    /**
     * The current globally available container (if any).
     *
     * @var static
     */
    protected static $instance;
    /**
     * @var Definition\DefinitionInterface[]
     */
    protected $definitions = [];
    /**
     * @var \Nip\Container\ServiceProvider\ProviderRepository
     */
    protected $providers = null;
    /**
     * The container's shared instances.
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Set the globally available instance of the container.
     *
     * @return static
     */
    public static function getInstance()
    {
        return static::$instance;
    }

    /**
     * Set the shared instance of the container.
     *
     * @param Container $container
     *
     * @return void
     */
    public static function setInstance(self $container)
    {
        static::$instance = $container;
    }

    /**
     * Register a shared binding in the container.
     *
     * @param string|array         $abstract
     * @param \Closure|string|null $concrete
     *
     * @return void
     */
    public function singleton($abstract, $concrete = null)
    {
        $this->add($abstract, $concrete, true);
    }

    /**
     * @param $id
     * @param null $concrete
     * @param bool $share
     *
     * @return ClassDefinition|null
     */
    public function add($id, $concrete = null, $share = false)
    {
        if (is_null($concrete)) {
            $concrete = $id;
        }

        $this->dropStaleInstances($id);

        $definition = $this->newDefinition($id, $concrete);
        if ($definition instanceof DefinitionInterface) {
            $definition->setShared($share);
            $this->definitions[$id] = $definition;

            return $definition;
        }

        // dealing with a value that cannot build a definition
        $this->instances[$id] = $concrete;

        return $concrete;
    }

    /**
     * Drop all of the stale instances and aliases.
     *
     * @param string $id
     *
     * @return void
     */
    protected function dropStaleInstances($id)
    {
        unset($this->instances[$id]);
    }

    /**
     * @param $id
     * @param $concrete
     *
     * @return ClassDefinition
     */
    public function newDefinition($id, $concrete)
    {
//        if (is_callable($concrete)) {
//            $concrete = new CallableDefinition($id, $concrete);
        if (is_string($concrete)) {
            $concrete = new ClassDefinition($id, $concrete);
        }
        // if the item is not defineable we just return the value to be stored
        // in the container as an arbitrary value/instance
        return $concrete;
    }

    /**
     * @param string $id
     * @param array  $args
     *
     * @return bool|mixed|object
     */
    public function get($id, array $args = [])
    {
        $instance = $this->getFromThisContainer($id, $args);

        if ($instance === false && $this->getProviders()->provides($id)) {
            $this->getProviders()->register($id);
            $instance = $this->getFromThisContainer($id, $args);
        }

        if ($instance !== false) {
            return $instance;
        }

        throw new NotFoundException(
            sprintf('Alias (%s) is not being managed by the container', $id)
        );
    }

    /**
     * @param $id
     * @param array $args
     *
     * @return bool|mixed
     */
    protected function getFromThisContainer($id, array $args = [])
    {
        if ($this->hasInstance($id)) {
            return $this->instances[$id];
        }

        if ($this->hasDefinition($id)) {
            $definition = $this->getDefinition($id);
            $instance = $definition->build($args);
            if ($definition->isShared()) {
                $this->instances[$id] = $instance;
            }

            return $instance;
        }

        return false;
    }

    /**
     * @param $id
     *
     * @return bool
     */
    protected function hasInstance($id)
    {
        return array_key_exists($id, $this->instances);
    }

    /**
     * @param $id
     *
     * @return bool
     */
    protected function hasDefinition($id)
    {
        return array_key_exists($id, $this->definitions);
    }

    /**
     * @param $id
     *
     * @return DefinitionInterface
     */
    public function getDefinition($id)
    {
        return $this->definitions[$id];
    }

    /**
     * @return ServiceProvider\ProviderRepository
     */
    public function getProviders()
    {
        if ($this->providers === null) {
            $this->initProviders();
        }

        return $this->providers;
    }

    /**
     * @param ServiceProvider\ProviderRepository $providers
     */
    public function setProviders($providers)
    {
        $this->providers = $providers;
    }

    public function initProviders()
    {
        $providers = (new ProviderRepository())->setContainer($this);
        $this->setProviders($providers);
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function has($id)
    {
        return $this->hasInstance($id) || $this->hasDefinition($id);
    }

    /**
     * @param $id
     * @param null $concrete
     */
    public function set($id, $concrete = null)
    {
        $this->instances[$id] = $concrete;
    }

    /**
     * Determine if a given offset exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
    }

    /**
     * Get the value at a given offset.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
    }

    /**
     * Set the value at a given offset.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function offsetSet($key, $value)
    {
    }

    /**
     * Unset the value at a given offset.
     *
     * @param string $key
     *
     * @return void
     */
    public function offsetUnset($key)
    {
    }

    /**
     * Dynamically access container services.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this[$key];
    }

    /**
     * Dynamically set container services.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this[$key] = $value;
    }

    /**
     * @param $provider
     *
     * @return $this
     */
    public function addServiceProvider($provider)
    {
        $this->getProviders()->add($provider);

        return $this;
    }
}
