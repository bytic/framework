<?php

namespace Nip\Tests\Records;

use \Nip_RecordCollection;
use \Nip_Records;

use \Mockery as m;

class RecordsTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var \Nip_Records
     */
    protected $_object;


    public function setUp()
    {
        $this->_object = new Nip_Records();
    }

    public function testGetRelationClass()
    {
        $this->assertEquals('Nip\Records\Relations\BelongsTo', $this->_object->getRelationClass('BelongsTo'));
        $this->assertEquals('Nip\Records\Relations\BelongsTo', $this->_object->getRelationClass('belongsTo'));

        $this->assertEquals('Nip\Records\Relations\HasMany', $this->_object->getRelationClass('HasMany'));
        $this->assertEquals('Nip\Records\Relations\HasMany', $this->_object->getRelationClass('hasMany'));

        $this->assertEquals('Nip\Records\Relations\HasAndBelongsToMany', $this->_object->getRelationClass('HasAndBelongsToMany'));
        $this->assertEquals('Nip\Records\Relations\HasAndBelongsToMany', $this->_object->getRelationClass('hasAndBelongsToMany'));
    }

    public function testNewRelation()
    {
        $this->assertInstanceOf('Nip\Records\Relations\BelongsTo', $this->_object->newRelation('BelongsTo'));
        $this->assertInstanceOf('Nip\Records\Relations\BelongsTo', $this->_object->newRelation('belongsTo'));

        $this->assertInstanceOf('Nip\Records\Relations\HasMany', $this->_object->newRelation('HasMany'));
        $this->assertInstanceOf('Nip\Records\Relations\HasMany', $this->_object->newRelation('hasMany'));

        $this->assertInstanceOf('Nip\Records\Relations\HasAndBelongsToMany', $this->_object->newRelation('HasAndBelongsToMany'));
        $this->assertInstanceOf('Nip\Records\Relations\HasAndBelongsToMany', $this->_object->newRelation('hasAndBelongsToMany'));
    }

    public function testInitRelationsFromArrayBelongsTo()
    {
        $users = m::namedMock('Users', 'Nip_Records')->shouldDeferMissing()
            ->shouldReceive('instance')->andReturnSelf()->getMock();
        m::namedMock('User', 'Nip_Record');
        m::namedMock('Articles', 'Nip_Records');

//        $this->getMockBuilder('Nip_Records')->setMockClassName('Users')->getMock();
//        $this->getMockBuilder('Nip_Record')->setMockClassName('User')->getMock();
//        $this->getMockBuilder('Nip_Records')->setMockClassName('Articles')->getMock();

        $this->_object->initRelationsFromArray('belongsTo',array('User'));
        $this->assertTrue($this->_object->hasRelation('User'));
        $this->assertInstanceOf('Nip\Records\Relations\BelongsTo', $this->_object->getRelation('User'));
        $this->assertInstanceOf('Users', $this->_object->getRelation('User')->getWith());

        $this->_object->setPrimaryFK('id_object');
        $this->_object->initRelationsFromArray('belongsTo',array(
            'User' => array('with' => $users),
        ));
        $this->assertInstanceOf('Users', $this->_object->getRelation('User')->getWith());
        $this->assertSame($users, $this->_object->getRelation('User')->getWith());
        $this->assertEquals('id_object', $this->_object->getRelation('User')->getFK());

        $this->assertEquals('id_object', $this->_object->getRelation('User')->getQuery()->assemble());
    }
}