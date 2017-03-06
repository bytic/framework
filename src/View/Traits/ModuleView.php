<?php

namespace Nip\View;

use Nip\Mvc\Modules;

/**
 * Class ModuleView
 * @package Nip\View
 */
trait ModuleView
{
    public function generateModuleBasePath()
    {
        /** @var Modules $modules */
        $modules = app('mvc.modules');
        $path = $modules->getViewPath($this->getModuleName());
        $this->setBasePath($path);
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return 'default';
    }
}