<?php

namespace Nip\Container;

use League\Container\ServiceProvider\ServiceProviderInterface;

/**
 * Class ContainerAliasBindingsTrait
 * @package Nip\Container
 */
trait ContainerAliasBindingsTrait
{
    use ContainerAwareTrait;

    public function initContainer()
    {
        if ($this->hasContainer()) {
            return;
        }
        $this->setContainer($this->generateContainer());
        $this->initContainerBindings();
    }

    protected function initContainerBindings()
    {
    }

    /**
     * @return ContainerInterface
     */
    public function generateContainer()
    {
        return Container::getInstance();
    }

    /**
     * @param $alias
     * @return mixed
     */
    public function get($alias)
    {
        return $this->getContainer()->get($alias);
    }

    /**
     * @param $alias
     * @return mixed
     */
    public function has($alias)
    {
        return $this->getContainer()->has($alias);
    }

    /**
     * @param $alias
     * @param null $concrete
     * @param bool $share
     */
    public function set($alias, $concrete = null, $share = false)
    {
        $this->getContainer()->set($alias, $concrete, $share);
    }

    /**
     * @param $alias
     * @param null $concrete
     */
    public function share($alias, $concrete = null)
    {
        $this->getContainer()->share($alias, $concrete);
    }

    /**
     * @param string|ServiceProviderInterface $provider
     * @return void
     */
    public function addServiceProvider($provider)
    {
        $this->getContainer()->addServiceProvider($provider);
    }
}
