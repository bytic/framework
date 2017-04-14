<?php

namespace Nip\Filesystem;

use InvalidArgumentException;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\AdapterInterface;
use League\Flysystem\FilesystemInterface;
use Nip\Config\Config;

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
     * @var FileDisk[]
     */
    protected $disks = [];

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];


    /**
     * Create a new filesystem manager instance.
     *
     * @param  \Nip\Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Get a filesystem instance.
     *
     * @param  string $name
     * @return FileDisk
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
     * @return FileDisk
     */
    protected function get($name)
    {
        return isset($this->disks[$name]) ? $this->disks[$name] : $this->resolve($name);
    }

    /**
     * Resolve the given disk.
     *
     * @param  string $name
     * @return FileDisk
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name)->toArray();

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
     * @return Config
     */
    protected function getConfig($name)
    {
        return config("filesystems.disks.{$name}");
    }

    /**
     * Call a custom driver creator.
     *
     * @param  array $config
     * @return FileDisk
     */
    protected function callCustomCreator(array $config)
    {
        $driver = $this->customCreators[$config['driver']]($this->app, $config);
        if ($driver instanceof FilesystemInterface) {
            return $this->adapt($driver);
        }
        return $driver;
    }

    /**
     * Adapt the filesystem implementation.
     *
     * @param  \League\Flysystem\FilesystemInterface $filesystem
     * @return \League\Flysystem\FilesystemInterface|FileDisk
     */
    protected function adapt(FilesystemInterface $filesystem)
    {
        return $filesystem;
//        return new FlysystemAdapter($filesystem);
    }

    /**
     * Create an instance of the local driver.
     *
     * @param  array $config
     * @return \League\Flysystem\FilesystemInterface
     */
    public function createLocalDriver($config)
    {
        $permissions = isset($config['permissions']) ? $config['permissions'] : [];
        $links = [];
//        $links = Arr::get($config, 'links') === 'skip'
//            ? LocalAdapter::SKIP_LINKS
//            : LocalAdapter::DISALLOW_LINKS;

        return $this->adapt(
            $this->createDisk(
                new LocalAdapter(
                    $config['root'],
                    LOCK_EX,
                    $links,
                    $permissions
                ),
                $config
            )
        );
    }

    /**
     * Create a Flysystem instance with the given adapter.
     *
     * @param  \League\Flysystem\AdapterInterface $adapter
     * @param  array $config
     * @return FileDisk
     */
    protected function createDisk(AdapterInterface $adapter, $config)
    {
//        $config = Arr::only($config, ['visibility', 'disable_asserts', 'url']);
        return new FileDisk($adapter, count($config) > 0 ? $config : null);
    }

    /**
     * Set the given disk instance.
     *
     * @param  string $name
     * @param  FileDisk $disk
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
