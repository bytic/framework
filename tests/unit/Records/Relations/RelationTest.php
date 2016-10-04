<?php

namespace Nip\Tests\Unit\Records\Relations;

class RelationTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $stub = $this->getMockForAbstractClass('Nip\Records\Relations\Relation');
        $this->_object = $stub;
    }

    protected function _after()
    {
    }

    // tests
}