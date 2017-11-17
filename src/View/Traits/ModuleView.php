<?php

namespace Nip\View\Traits;

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
        $folderPath = $this->generateFolderBasePath();
        if (is_dir($folderPath)) {
            return $folderPath;
        }

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
    public function generateFolderBasePath()
    {
        return dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return 'default';
    }
}
