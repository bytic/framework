<?php

namespace Nip\Container\ServiceProvider;

use Nip\Container\ContainerAwareInterface;

/**
 * Interface ServiceProviderAggregateInterface.
 */
interface ProviderRepositoryInterface extends ContainerAwareInterface
{
    /**
     * Add a service provider to the aggregate.
     *
     * @param string|\Nip\Container\ServiceProvider\ServiceProviderInterface $provider
     *
     * @return $this
     */
    public function add($provider);

    /**
     * Determines whether a service is provided by the aggregate.
     *
     * @param string $service
     *
     * @return bool
     */
    public function provides($service);

    /**
     * Invokes the register method of a provider that provides a specific service.
     *
     * @param string $service
     *
     * @return void
     */
    public function register($service);
}
