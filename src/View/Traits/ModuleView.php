<?php

namespace Nip\View;

use Nip\Mvc\Modules;

/**
 * Class ModuleView
 * @package Nip\View
 */
trait ModuleView
{

    /**
     * @return string
     */
    protected function generateBasePath()
    {
        return $this->generateModuleBasePath();
    }

    /**
     * @return string
     */
    public function generateModuleBasePath()
    {
        /** @var Modules $modules */
        $modules = app('mvc.modules');
        return $modules->getViewPath($this->getModuleName());
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return 'default';
    }
}