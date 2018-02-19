<?php

namespace Nip\Application\Bootstrap\Bootstrapers;

use Nip\Application;

/**
 * Interface BootstraperInterface
 * @package Nip\Application\Bootstrap\Bootstrapers
 */
interface BootstraperInterface
{
    /**
     * @param Application $app
     * @return mixed
     */
    public function bootstrap(Application $app);
}