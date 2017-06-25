<?php

namespace Nip\Http\Kernel;

use Nip\Application;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Interface KernelInterface
 * @package Nip\Http\Kernel
 */
interface KernelInterface extends HttpKernelInterface
{
    /**
     * Get the Laravel application instance.
     *
     * @return Application
     */
    public function getApplication();
}
