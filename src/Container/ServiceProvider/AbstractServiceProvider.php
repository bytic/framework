<?php

namespace Nip\Container\ServiceProvider;

use Nip\Container\ContainerAwareTrait;

/**
 * Class AbstractServiceProvider.
 *
 * @inspiration https://github.com/thephpleague/container/blob/master/src/ServiceProvider/AbstractServiceProvider.php
 */
abstract class AbstractServiceProvider implements ServiceProviderInterface
{
    use ContainerAwareTrait;

    /**
     * @param null|string $service
     *
     * @return bool
     */
    public function isProviding($service)
    {
        return in_array($service, $this->provides());
    }
}
