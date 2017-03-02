<?php

namespace Nip\Application\Bootstrap;

use Nip\Application\Bootstrap\Bootstrapers\LoadConfiguration;
use Nip\Application\Bootstrap\Bootstrapers\RegisterContainer;

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
        LoadConfiguration::class,
    ];
}