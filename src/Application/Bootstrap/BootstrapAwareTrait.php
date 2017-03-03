<?php

namespace Nip\Application\Bootstrap;

use Nip\Application\Bootstrap\Bootstrapers\AbstractBootstraper;
use Nip\Container\ContainerInterface;

/**
 * Class BootstrapAwareTrait
 * @package Nip\Application\Bootstrap
 */
trait BootstrapAwareTrait
{

    /**
     * Indicates if the application has been bootstrapped before.
     *
     * @var bool
     */
    protected $hasBeenBootstrapped = false;

    /**
     * The bootstrap classes for the application.
     *
     * @var null|AbstractBootstraper[]
     */
    protected $bootstrappers = null;

    /**
     * Bootstrap the application for HTTP requests.
     *
     * @return void
     */
    public function bootstrap()
    {
        if (!$this->hasBeenBootstrapped()) {
            $this->bootstrapWith($this->bootstrappers());
        }
    }

    /**
     * Determine if the application has been bootstrapped before.
     *
     * @return bool
     */
    public function hasBeenBootstrapped()
    {
        return $this->hasBeenBootstrapped;
    }

    /**
     * Run the given array of bootstrap classes.
     *
     * @param  array $bootstrappers
     * @return void
     */
    public function bootstrapWith(array $bootstrappers)
    {
        $this->hasBeenBootstrapped = true;
        foreach ($bootstrappers as $bootstrapper) {
            $this->getBootstrap($bootstrapper)->bootstrap($this);
        }
    }

    /**
     * @param $bootstrapper
     * @return AbstractBootstraper
     */
    public function getBootstrap($bootstrapper)
    {
        if ($this->getContainer() instanceof ContainerInterface) {
            return $this->get($bootstrapper);
        }
        return new $bootstrapper;
    }

    /**
     * Get the bootstrap classes for the application.
     *
     * @return array
     */
    protected function bootstrappers()
    {
        if ($this->bootstrappers === null) {
            $this->initBootstrappers();
        }
        return $this->bootstrappers;
    }

    /**
     * @param AbstractBootstraper $bootstrapper
     */
    protected function addBootstrapper($bootstrapper)
    {
        $this->bootstrappers[] = $bootstrapper;
    }

    protected function initBootstrappers()
    {
        $this->bootstrappers = $this->getDefaultBootstrappers();
    }

    /**
     * @return AbstractBootstraper[]
     */
    protected function getDefaultBootstrappers()
    {
        return [];
    }
}
