<?php

namespace Nip\Container\Definition;

use ReflectionClass;

class ClassDefinition extends AbstractDefinition implements DefinitionInterface
{
    /**
     * @var array
     */
    protected $methods = [];


    public function build(array $args = [])
    {
//        $args = (empty($args)) ? $this->arguments : $args;
        $reflection = new ReflectionClass($this->concrete);
        $instance = $reflection->newInstanceArgs();
        return $instance;
    }
}