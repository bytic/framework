<?php

namespace Nip\Application\Bootstrap;

use Nip\Application\Bootstrap\Bootstrapers\AbstractBootstraper;
use Nip\Application\Bootstrap\Bootstrapers\HandleExceptions;
use Nip\Application\Bootstrap\Bootstrapers\LoadConfiguration;
use Nip\Application\Bootstrap\Bootstrapers\RegisterContainer;
use Nip\Application\Bootstrap\Bootstrapers\RegisterProviders;

/**
 * Class CoreBootstrapersTrait
 * @package Nip\Application\Bootstrap
 */
trait CoreBootstrapersTrait
{
    use BootstrapAwareTrait;


    /**
     * @return AbstractBootstraper[]
     */
    protected function getDefaultBootstrappers()
    {
        return [
            RegisterContainer::class,
            HandleExceptions::class,
            LoadConfiguration::class,
            RegisterProviders::class,
        ];
    }
}
