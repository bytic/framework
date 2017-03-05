<?php

namespace Nip\Tests\Unit\Records\Relations;

/**
 * Class RelationTest
 * @package Nip\Tests\Unit\Records\Relations
 */
class RelationTest extends \Codeception\TestCase\Test
{

    protected function _before()
    {
        $stub = $this->getMockForAbstractClass('Nip\Records\Relations\Relation');
        $this->_object = $stub;
    }
}