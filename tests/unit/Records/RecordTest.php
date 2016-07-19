<?php

namespace Nip\Tests\Records;

use Mockery as m;
use \Nip\Database\Connection;
use Nip_Records;
use Nip_Record;

class RecordTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var \Nip_Record
     */
    protected $_object;

    protected function _before()
    {
        $wrapper = new Connection();

        $manager = new Nip_Records();
        $manager->setDB($wrapper);
        $manager->setTable('pages');

        $this->_object = new Nip_Record();
        $this->_object->setManager($manager);
    }

    protected function _after()
    {
    }

    // tests

    public function testNewRelation()
    {
        $users = m::namedMock('Users', 'Nip_Records')->shouldDeferMissing()
            ->shouldReceive('instance')->andReturnSelf()->getMock();
        m::namedMock('User', 'Nip_Record');

        $this->_object->getManager()->initRelationsFromArray('belongsTo', array('User'));

        $relation = $this->_object->newRelation('User');
        $this->assertSame($users, $relation->getWith());
        $this->assertSame($this->_object, $relation->getItem());
    }

}