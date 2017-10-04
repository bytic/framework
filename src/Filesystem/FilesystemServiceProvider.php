<?php

namespace Nip\Filesystem;

use Nip\Container\ServiceProviders\Providers\AbstractServiceProvider;

/**
 * Class FilesystemServiceProvider
 * @package Nip\Filesystem
 *
 * @inspiration https://github.com/laravel/framework/blob/5.4/src/Illuminate/Filesystem/FilesystemServiceProvider.php
 *
 */
class FilesystemServiceProvider extends AbstractServiceProvider
{

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return [
            'files',
            'filesystem', 'filesystem.disk'
        ];
    }

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->registerNativeFilesystem();
        $this->registerFlysystem();
    }

    /**
     * Register the native filesystem implementation.
     *
     * @return void
     */
    protected function registerNativeFilesystem()
    {
        $this->getContainer()->share('files', function () {
            return new Filesystem;
        });
    }

    /**
     * Register the driver based filesystem.
     *
     * @return void
     */
    protected function registerFlysystem()
    {
        $this->registerManager();

        $this->getContainer()->share('filesystem.disk', function () {
            return app('filesystem')->disk($this->getDefaultDriver());
        });
        $this->getContainer()->share('filesystem.cloud', function () {
            return app('filesystem')->disk($this->getCloudDriver());
        });
    }

    /**
     * Register the filesystem manager.
     *
     * @return void
     */
    protected function registerManager()
    {
        $this->getContainer()->share('filesystem', function () {
            return new FilesystemManager($this->getContainer()->get('app'));
        });
    }

    /**
     * Get the default file driver.
     *
     * @return string
     */
    protected function getDefaultDriver()
    {
        return config('filesystems.default');
    }

    /**
     * Get the default cloud based file driver.
     *
     * @return string
     */
    protected function getCloudDriver()
    {
        return config('filesystems.cloud');
    }
}
