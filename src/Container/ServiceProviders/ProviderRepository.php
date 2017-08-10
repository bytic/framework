<?php

namespace Nip\Container\ServiceProviders;

use Nip\Container\ContainerAwareInterface;
use Nip\Container\ContainerAwareTrait;
use Nip\Container\ServiceProviders\Providers\AbstractServiceProvider;
use Nip\Container\ServiceProviders\Providers\BootableServiceProviderInterface;
use Nip\Container\ServiceProviders\Providers\ServiceProviderInterface;

/**
 * Class ServiceProviderAggregate
 * @package Nip\Container\ServiceProvider
 */
class ProviderRepository implements ProviderRepositoryInterface
{
    use ContainerAwareTrait;

    /**
     * @var array
     */
    protected $services = [];

    /**
     * @var AbstractServiceProvider[]
     */
    protected $providers = [];

    /**
     * @var array Array of registered providers class names
     */
    protected $registeredProviders = [];

    /**
     * The deferred services and their providers.
     *
     * @var array
     */
    protected $deferredServices = [];

    /**
     * Indicates if the application has "booted".
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * Adds a new Service Provider
     * {@inheritdoc}
     */
    public function add($provider)
    {
        if (is_string($provider) && class_exists($provider)) {
            $provider = $this->resolveProvider($provider);
        }

        if ($provider instanceof ContainerAwareInterface) {
            $provider->setContainer($this->getContainer());
        }

        if ($provider instanceof ServiceProviderInterface) {
            foreach ($provider->provides() as $service) {
                $this->services[$service] = get_class($provider);
            }
            $this->providers[] = $provider;
            return $this;
        }

        throw new \InvalidArgumentException(
            'A service provider must be a fully qualified class name or instance ' .
            'of (\Nip\Container\ServiceProvider\ServiceProviderInterface)'
        );
    }

    /**
     * Resolve a service provider instance from the class name.
     *
     * @param  string $provider
     * @return AbstractServiceProvider
     */
    public function resolveProvider($provider)
    {
        return new $provider($this);
    }

    public function register()
    {
        foreach ($this->providers as $provider) {
            $this->registerProvider($provider);
        }
    }

    /**
     * Register a service provider with the application.
     *
     * @param  AbstractServiceProvider|string $provider
     * @return AbstractServiceProvider
     */
    public function registerProvider($provider)
    {
        if (($provider = $this->getProvider($provider))) {
        } elseif (is_string($provider)) {
            $provider = $this->resolveProvider($provider);
        }

        if ($this->registeredProvider($provider)) {
            return $provider;
        }

        if (method_exists($provider, 'register')) {
            $provider->register();
        }

        $this->markAsRegistered($provider);

        // If the application has already booted, we will call this boot method on
        // the provider class so it has an opportunity to do its boot logic and
        // will be ready for any usage by this developer's application logic.
        if ($this->booted) {
            $this->bootProvider($provider);
        }
        return $provider;
    }

    /**
     * Get the registered service provider instance if it exists.
     *
     * @param  AbstractServiceProvider|string $provider
     * @return AbstractServiceProvider|null
     */
    public function getProvider($provider)
    {
        $name = is_string($provider) ? $provider : get_class($provider);
        return \Nip_Helper_Arrays::first($this->providers, function ($value) use ($name) {
            return $value instanceof $name;
        });
    }

    /**
     * @param AbstractServiceProvider $provider
     * @return bool
     */
    public function registeredProvider($provider)
    {
        $providerClass = get_class($provider);
        if (isset($this->registeredProviders[$providerClass]) && $this->registeredProviders[$providerClass] === true) {
            return true;
        }
        return false;
    }

    /**
     * Mark the given provider as registered.
     *
     * @param  AbstractServiceProvider $provider
     * @return void
     */
    protected function markAsRegistered($provider)
    {
        $this->registeredProviders[get_class($provider)] = true;
    }

    /**
     * Boot the given service provider.
     *
     * @param  AbstractServiceProvider $provider
     * @return mixed
     */
    protected function bootProvider(AbstractServiceProvider $provider)
    {
        if ($provider instanceof BootableServiceProviderInterface) {
            return $provider->boot();
        }
        return null;
    }

    /**
     * Check to see if the services is registered
     * {@inheritdoc}
     */
    public function provides($service)
    {
        return array_key_exists($service, $this->providers);
    }

    public function boot()
    {
        foreach ($this->providers as $provider) {
            $this->bootProvider($provider);
        }

        $this->setBooted(true);
    }

    /**
     * @param bool $booted
     */
    public function setBooted($booted)
    {
        $this->booted = $booted;
    }

    /**
     * Load and boot all of the remaining deferred providers.
     *
     * @return void
     */
    public function loadDeferredProviders()
    {
        // We will simply spin through each of the deferred providers and register each
        // one and boot them if the application has booted. This should make each of
        // the remaining services available to this application for immediate use.
        foreach ($this->deferredServices as $service => $provider) {
            $this->loadDeferredProvider($service);
        }
        $this->deferredServices = [];
    }

    /**
     * Load the provider for a deferred service.
     *
     * @param  string $service
     * @return void
     */
    public function loadDeferredProvider($service)
    {
        if (!isset($this->deferredServices[$service])) {
            return;
        }
        $provider = $this->deferredServices[$service];

        // If the service provider has not already been loaded and registered we can
        // register it with the application and remove the service from this list
        // of deferred services, since it will already be loaded on subsequent.
        if (!isset($this->registeredProviders[$provider])) {
            $this->registerDeferredProvider($provider, $service);
        }
    }

    /**
     * Register a deferred provider and service.
     *
     * @param  string $provider
     * @param  string $service
     * @return void
     */
    public function registerDeferredProvider($provider, $service = null)
    {
        // Once the provider that provides the deferred service has been registered we
        // will remove it from our local list of the deferred services with related
        // providers so that this container does not try to resolve it out again.
        if ($service) {
            unset($this->deferredServices[$service]);
        }
        $this->registerProvider($instance = new $provider($this));
    }
}
