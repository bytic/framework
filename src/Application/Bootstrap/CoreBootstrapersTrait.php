<?php

namespace Nip\Application\Bootstrap;

use Nip\Application\Bootstrap\Bootstrapers\HandleExceptions;
use Nip\Application\Bootstrap\Bootstrapers\LoadConfiguration;
use Nip\Application\Bootstrap\Bootstrapers\RegisterContainer;
use Nip\Application\Bootstrap\Bootstrapers\RegisterProviders;

trait CoreBootstrapersTrait
{
    use BootstrapAwareTrait;

    /**
     * The bootstrap classes for the application.
     *
     * @var array
     */
    protected $bootstrappers = [
        RegisterContainer::class,
        HandleExceptions::class,
        LoadConfiguration::class,
        RegisterProviders::class,
    ];
}