<?php

namespace Nip\Container\ServiceProvider;

use Nip\Container\ServiceProvider\AbstractServiceProvider as AbstractProvider;
use Nip\Container\ServiceProvider\SignatureServiceProviderInterface as AbstractInterface;

/**
 * Class AbstractSignatureServiceProvider.
 */
abstract class AbstractSignatureServiceProvider extends AbstractProvider implements AbstractInterface
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
