<?php

namespace Nip\View\Traits;

use Nip\Mvc\Modules;
use ReflectionClass;

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
        $reflector = new ReflectionClass(get_class($this));
        $dirName = dirname($reflector->getFileName());

        return dirname(dirname($dirName)).DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return 'default';
    }
}
