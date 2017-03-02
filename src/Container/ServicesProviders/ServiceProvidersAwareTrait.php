<?php

namespace Nip\Container\ServiceProviders;

trait ServiceProviderAwareTrait
{
    /**
     * All of the registered service providers.
     *
     * @var ProviderRepository
     */
    protected $providerRepository = null;

    public function registerConfiguredProviders()
    {
        $providers = $this->getConfiguredProviders();
        foreach ($providers as $provider) {
            $this->getProviderRepository()->add($provider);
        }
        return $this->getProviderRepository()->register();
    }

    /**
     * @return array
     */
    public function getConfiguredProviders()
    {
        return [];
    }

    /**
     * @return ProviderRepository
     */
    public function getProviderRepository()
    {
        if ($this->providerRepository === null) {
            $this->providerRepository = new ProviderRepository();
            $this->providerRepository->setContainer($this->getContainer());
        }
        return $this->providerRepository;
    }

    public function bootProviders()
    {
        $this->getProviderRepository()->boot();
    }
}
