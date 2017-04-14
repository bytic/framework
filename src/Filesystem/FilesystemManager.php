<?php

namespace Nip\Filesystem;

use InvalidArgumentException;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\FilesystemInterface;
use Nip\Utility\Arr;

/**
 * Class FilesystemManager
 * @package Nip\Filesystem
 */
class FilesystemManager
{
    /**
     * The application instance.
     *
     * @var \Nip\Application
     */
    protected $app;
    /**
     * The array of resolved filesystem drivers.
     *
     * @var array
     */
    protected $disks = [];


    /**
     * Create a new filesystem manager instance.
     *
     * @param  \Nip\Application $app
     *
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Get a filesystem instance.
     *
     * @param  string $name
     * @return Filesystem
     */
    public function disk($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();
        return $this->disks[$name] = $this->get($name);
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return config('filesystems.default');
    }

    /**
     * Attempt to get the disk from the local cache.
     *
     * @param  string $name
     * @return Filesystem
     */
    protected function get($name)
    {
        return isset($this->disks[$name]) ? $this->disks[$name] : $this->resolve($name);
    }

    /**
     * Resolve the given disk.
     *
     * @param  string $name
     * @return Filesystem
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($config);
        }
        $driverMethod = 'create' . ucfirst($config['driver']) . 'Driver';
        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        } else {
            throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
        }
    }

    /**
     * Get the filesystem connection configuration.
     *
     * @param  string $name
     * @return array
     */
    protected function getConfig($name)
    {
        return config("filesystems.disks.{$name}");
    }

    /**
     * Create an instance of the local driver.
     *
     * @param  array $config
     * @return Filesystem
     */
    public function createLocalDriver(array $config)
    {
        $permissions = isset($config['permissions']) ? $config['permissions'] : [];
        $links = Arr::get($config, 'links') === 'skip'
            ? LocalAdapter::SKIP_LINKS
            : LocalAdapter::DISALLOW_LINKS;

        return $this->adapt($this->createFlysystem(
            new LocalAdapter(
                $config['root'], LOCK_EX, $links, $permissions
            ),
            $config
        )
        );
    }

    /**
     * Adapt the filesystem implementation.
     *
     * @param  \League\Flysystem\FilesystemInterface $filesystem
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function adapt(FilesystemInterface $filesystem)
    {
        return new FilesystemAdapter($filesystem);
    }

    /**
     * Create a Flysystem instance with the given adapter.
     *
     * @param  \League\Flysystem\AdapterInterface $adapter
     * @param  array $config
     * @return FilesystemInterface
     */
    protected function createFlysystem(AdapterInterface $adapter, array $config)
    {
        $config = Arr::only($config, ['visibility', 'disable_asserts', 'url']);
        return new Flysystem($adapter, count($config) > 0 ? $config : null);
    }

    /**
     * Set the given disk instance.
     *
     * @param  string $name
     * @param  mixed $disk
     * @return void
     */
    public function set($name, $disk)
    {
        $this->disks[$name] = $disk;
    }

    /**
     * Get the default cloud driver name.
     *
     * @return string
     */
    public function getDefaultCloudDriver()
    {
        return config('filesystems.cloud');
    }
}
