<?php

namespace Nip\Container\Bridges;

use League\Container\Container as Container;
use LogicException;
use Nip\Container\Traits\ContainerArrayAccessTrait;

/**
 * Class LeagueContainer
 * @package Nip\Container\Bridges
 */
abstract class LeagueContainer extends Container implements BridgeInterface
{

    /**
     * The registered type aliases.
     *
     * @var array
     */
    protected $aliases = [];

    use ContainerArrayAccessTrait;

    /**
     * @inheritdoc
     */
    public function set($alias, $concrete = null, $share = false)
    {
        return $this->add($alias, $concrete, $share);
    }

    /**
     * @inheritdoc
     */
    public function remove($alias)
    {
    }

    /**
     * Determine if a given string is an alias.
     *
     * @param  string $name
     * @return bool
     */
    public function isAlias($name)
    {
        return isset($this->aliases[$name]);
    }

    /**
     * Alias a type to a different name.
     *
     * @param  string $abstract
     * @param  string $alias
     * @return void
     */
    public function alias($abstract, $alias)
    {
        $this->aliases[$alias] = $abstract;
//        $this->abstractAliases[$abstract][] = $alias;

        $this->share($alias, function () use ($abstract) {
            return $this->get($abstract);
        });
    }

    /**
     * Get the alias for an abstract if available.
     *
     * @param  string $abstract
     * @return string
     *
     * @throws \LogicException
     */
    public function getAlias($abstract)
    {
        if (!isset($this->aliases[$abstract])) {
            return $abstract;
        }
        if ($this->aliases[$abstract] === $abstract) {
            throw new LogicException("[{$abstract}] is aliased to itself.");
        }
        return $this->getAlias($this->aliases[$abstract]);
    }
}
