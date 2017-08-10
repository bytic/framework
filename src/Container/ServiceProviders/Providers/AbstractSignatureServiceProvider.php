<?php

namespace Nip\Container\ServiceProviders\Providers;

use Nip\Container\ServiceProviders\Providers\SignatureServiceProviderInterface as SignatureInterface;

/**
 * Class AbstractSignatureServiceProvider
 * @package Nip\Container\ServiceProvider
 */
abstract class AbstractSignatureServiceProvider extends AbstractServiceProvider implements SignatureInterface
{
    /**
     * @var string
     */
    protected $signature;

    /**
     * {@inheritdoc}
     */
    public function withSignature($signature)
    {
        $this->signature = $signature;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSignature()
    {
        return (is_null($this->signature)) ? get_class($this) : $this->signature;
    }
}
