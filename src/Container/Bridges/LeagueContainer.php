<?php

namespace Nip\Container\Bridges;

use League\Container\Container as Container;
use Nip\Container\Traits\ContainerArrayAccessTrait;

/**
 * Class LeagueContainer
 * @package Nip\Container\Bridges
 */
abstract class LeagueContainer extends Container implements BridgeInterface
{
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
}
