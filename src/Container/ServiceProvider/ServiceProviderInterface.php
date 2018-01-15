<?php

namespace Nip\Container\ServiceProvider;

use Nip\Container\ContainerAwareInterface;

/**
 * Class AbstractServiceProvider.
 *
 * @inspiration https://github.com/thephpleague/container/blob/master/src/ServiceProvider/AbstractServiceProvider.php
 */
interface ServiceProviderInterface extends ContainerAwareInterface
{
    /**
     * Returns a boolean if checking whether this provider provides a specific
     * service or returns an array of provided services if no argument passed.
     *
     * @return array
     */
    public function provides();

    /**
     * Returns a boolean if checking whether this provider provides a specific
     * service or returns an array of provided services if no argument passed.
     *
     * @param string $service
     *
     * @return bool|array
     */
    public function isProviding($service);

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register();
}
