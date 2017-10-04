<?php

namespace Nip\Container\Bridges;

use League\Container\Container as Container;

/**
 * Class LeagueContainer
 * @package Nip\Container\Bridges
 */
abstract class LaravelContainer extends Container implements BridgeInterface
{
    public function share($alias, $concrete = null)
    {
        $this->singleton($alias, $concrete);
    }

    public function add($alias, $concrete = null, $share = false)
    {
        return $this->bind($alias, $concrete, $share);
    }
}
