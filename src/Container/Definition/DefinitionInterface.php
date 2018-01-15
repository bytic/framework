<?php

namespace Nip\Container\Definition;

/**
 * Interface DefinitionInterface.
 */
interface DefinitionInterface
{
    /**
     * Handle instantiation and manipulation of value and return.
     *
     * @param array $args
     *
     * @return mixed
     */
    public function build(array $args = []);

    /**
     * @return bool
     */
    public function isShared();
}
