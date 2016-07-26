<?php

namespace Nip\Tests\Records;

use Mockery as m;
use Nip\Database\Connection;
use Nip\Records\RecordManager as Records;
use Nip\Records\Record;

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

    protected function _before()
    {
        $wrapper = new Connection();

        $manager = new Records();
        $manager->setDB($wrapper);
        $manager->setTable('pages');

        $this->_object = new Record();
        $this->_object->setManager($manager);
    }

    protected function _after()
    {
    }

    // tests

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

}