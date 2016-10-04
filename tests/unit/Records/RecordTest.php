<?php

namespace Nip\Tests\Unit\Records;

use Mockery as m;
use Nip\Database\Connection;
use Nip\Records\Record;
use Nip\Records\RecordManager as Records;

class RecordTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Record
     */
    protected $_object;

    public function testNewRelation()
    {
        $users = m::namedMock('Users', 'Nip\Records\RecordManager')->shouldDeferMissing()
            ->shouldReceive('instance')->andReturnSelf()->getMock();
        m::namedMock('User', 'Record');

        $this->_object->getManager()->initRelationsFromArray('belongsTo', array('User'));

        $relation = $this->_object->newRelation('User');
        $this->assertSame($users, $relation->getWith());
        $this->assertSame($this->_object, $relation->getItem());
    }

    protected function _before()
    {
        $wrapper = new Connection();

        $manager = new Records();
        $manager->setDB($wrapper);
        $manager->setTable('pages');

        $this->_object = new Record();
        $this->_object->setManager($manager);
    }

    // tests

    protected function _after()
    {
    }

}