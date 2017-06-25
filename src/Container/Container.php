<?php

namespace Nip\Container;

use League\Container\ReflectionContainer;
use Nip\Container\Bridges\LeagueContainer;

/**
 * Class Container
 * @package Nip\Container
 */
class Container extends LeagueContainer implements ContainerInterface
{
    use Traits\ContainerPersistenceTrait;

    /**
     * @inheritdoc
     */
    public function __construct($providers = null, $inflectors = null, $definitionFactory = null)
    {
        parent::__construct($providers, $inflectors, $definitionFactory);

        // register the reflection container as a delegate to enable auto wiring
        $this->delegate(
            new ReflectionContainer
        );
    }
}
