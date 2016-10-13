<?php

namespace Nip\Container\Definition;

/**
 * Interface DefinitionInterface
 * @package Nip\Container\Definition
 */
interface DefinitionInterface
{
    /**
     * Handle instantiation and manipulation of value and return.
     *
     * @param  array $args
     * @return mixed
     */
    public function build(array $args = []);

    /**
     * @return boolean
     */
    public function isShared();
}
