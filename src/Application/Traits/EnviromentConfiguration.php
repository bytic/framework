<?php

namespace Nip\Application\Traits;

/**
 * Class EnviromentConfiguration
 * @package Nip\Application\Traits
 */
trait EnviromentConfiguration
{
    /**
     * The environment file to load during bootstrapping.
     *
     * @var string
     */
    protected $environmentFile = '.env';

    /**
     * Get the path to the environment file directory.
     *
     * @return string
     */
    public function environmentPath()
    {
        return $this->environmentPath ?: $this->basePath;
    }

    /**
     * Set the directory for the environment file.
     *
     * @param  string $path
     * @return $this
     */
    public function useEnvironmentPath($path)
    {
        $this->environmentPath = $path;
        return $this;
    }

    /**
     * Set the environment file to be loaded during bootstrapping.
     *
     * @param  string $file
     * @return $this
     */
    public function loadEnvironmentFrom($file)
    {
        $this->environmentFile = $file;
        return $this;
    }

    /**
     * Get the environment file the application is using.
     *
     * @return string
     */
    public function environmentFile()
    {
        return $this->environmentFile ?: '.env';
    }

    /**
     * Get the fully qualified path to the environment file.
     *
     * @return string
     */
    public function environmentFilePath()
    {
        return $this->environmentPath() . '/' . $this->environmentFile();
    }

    /**
     * Get or check the current application environment.
     *
     * @return string|bool
     */
    public function environment()
    {
        if (func_num_args() > 0) {
            $patterns = is_array(func_get_arg(0)) ? func_get_arg(0) : func_get_args();
            foreach ($patterns as $pattern) {
                if ($pattern == $this['env']) {
                    return true;
                }
            }
            return false;
        }
        return $this['env'];
    }
}
