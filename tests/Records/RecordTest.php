<?php

namespace Nip\Tests\Records;

use Mockery as m;
use Nip\Database\Connections\Connection;
use Nip\Records\Record;
use Nip\Records\RecordManager as Records;
use Nip\Tests\AbstractTest;

/**
 * Class RecordTest
 * @package Nip\Tests\Records
 */
class RecordTest extends AbstractTest
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Record
     */
    protected $object;

    /**
     * @return array
     */
    public function providerGetManagerName()
    {
        return [
            ["Notifications_Table", "Notifications_Tables"],
            ["Donation", "Donations"],
        ];
    }

    /**
     * @dataProvider providerGetManagerName
     * @param string $recordName
     * @param string $managerName
     */
    public function testGetManagerName($recordName, $managerName)
    {
        $this->object->setClassName($recordName);
        self::assertSame($managerName, $this->object->getManagerName());
    }

    public function testNewRelation()
    {
        $users = m::namedMock('Users', Records::class)->shouldDeferMissing()
            ->shouldReceive('instance')->andReturnSelf()->getMock();

        m::namedMock('User', Record::class);

        $this->object->getManager()->initRelationsFromArray('belongsTo', ['User']);

        $relation = $this->object->newRelation('User');
        static::assertSame($users, $relation->getWith());
        static::assertSame($this->object, $relation->getItem());
    }

    protected function setUp()
    {
        parent::setUp();
        $wrapper = new Connection(false);

        $manager = new Records();
        $manager->setDB($wrapper);
        $manager->setTable('pages');

        $this->object = new Record();
        $this->object->setManager($manager);
    }
}
