<?php

namespace Nip\Tests\Records;

use Mockery as m;
use Nip\Database\Connection;
use Nip\Records\RecordManager as Records;
use Nip\Request;

class RecordsTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Records
     */
    protected $_object;

    protected function _before()
    {
        $wrapper = new Connection();

        $this->_object = new Records();
        $this->_object->setDB($wrapper);
        $this->_object->setTable('pages');
    }

    protected function _after()
    {
    }

    // tests

    public function testSetModel()
    {
        $this->_object->setModel('Row');
        $this->assertEquals($this->_object->getModel(), 'Row');

        $this->_object->setModel('Row2');
        $this->assertEquals($this->_object->getModel(), 'Row2');
    }

    public function testGetFullNameTable()
    {
        $this->assertEquals('pages', $this->_object->getFullNameTable());

        $this->_object->getDB()->setDatabase('database_name');
        $this->assertEquals('database_name.pages', $this->_object->getFullNameTable());
    }

    public function testGenerateModelClass()
    {
        $this->assertEquals($this->_object->generateModelClass('Notifications\Table'), 'Notifications\Row');
        $this->assertEquals($this->_object->generateModelClass('Notifications_Tables'), 'Notifications_Table');
        $this->assertEquals($this->_object->generateModelClass('Notifications'), 'Notification');
        $this->assertEquals($this->_object->generateModelClass('Persons'), 'Person');
    }

    public function testGetRelationClass()
    {
        $this->assertEquals('Nip\Records\Relations\BelongsTo', $this->_object->getRelationClass('BelongsTo'));
        $this->assertEquals('Nip\Records\Relations\BelongsTo', $this->_object->getRelationClass('belongsTo'));

        $this->assertEquals('Nip\Records\Relations\HasMany', $this->_object->getRelationClass('HasMany'));
        $this->assertEquals('Nip\Records\Relations\HasMany', $this->_object->getRelationClass('hasMany'));

        $this->assertEquals('Nip\Records\Relations\HasAndBelongsToMany',
            $this->_object->getRelationClass('HasAndBelongsToMany'));
        $this->assertEquals('Nip\Records\Relations\HasAndBelongsToMany',
            $this->_object->getRelationClass('hasAndBelongsToMany'));
    }

    public function testNewRelation()
    {
        $this->assertInstanceOf('Nip\Records\Relations\BelongsTo', $this->_object->newRelation('BelongsTo'));
        $this->assertInstanceOf('Nip\Records\Relations\BelongsTo', $this->_object->newRelation('belongsTo'));

        $this->assertInstanceOf('Nip\Records\Relations\HasMany', $this->_object->newRelation('HasMany'));
        $this->assertInstanceOf('Nip\Records\Relations\HasMany', $this->_object->newRelation('hasMany'));

        $this->assertInstanceOf('Nip\Records\Relations\HasAndBelongsToMany',
            $this->_object->newRelation('HasAndBelongsToMany'));
        $this->assertInstanceOf('Nip\Records\Relations\HasAndBelongsToMany',
            $this->_object->newRelation('hasAndBelongsToMany'));
    }

    public function testInitRelationsFromArrayBelongsToSimple()
    {
        $users = m::namedMock('Users', 'Records')->shouldDeferMissing()
            ->shouldReceive('instance')->andReturnSelf()
            ->getMock();
        $users->setPrimaryFK('id_user');
        m::namedMock('User', 'Record');
        m::namedMock('Articles', 'Records');

        $this->_object->setPrimaryFK('id_object');

        $this->_object->initRelationsFromArray('belongsTo', array('User'));
        $this->_testInitRelationsFromArrayBelongsToUser('User');


        $this->_object->initRelationsFromArray('belongsTo', array(
            'UserName' => array('with' => $users),
        ));
        $this->_testInitRelationsFromArrayBelongsToUser('UserName');
        $this->assertSame($users, $this->_object->getRelation('User')->getWith());
    }


    protected function _testInitRelationsFromArrayBelongsToUser($name)
    {
        $this->assertTrue($this->_object->hasRelation($name));
        $this->assertInstanceOf('Nip\Records\Relations\BelongsTo', $this->_object->getRelation($name));
        $this->assertInstanceOf('Nip\Records\RecordManager', $this->_object->getRelation($name)->getWith());
        $this->assertEquals($this->_object->getRelation($name)->getWith()->getPrimaryFK(),
            $this->_object->getRelation($name)->getFK());
    }

    public function testNewCollection()
    {
        $collection = $this->_object->newCollection();
        $this->assertInstanceOf('Nip\Records\Collections\Collection', $collection);
        $this->assertSame($this->_object, $collection->getManager());
    }

    public function testRequestFilters()
    {
        $request = new Request();
        $params = array(
            'title' => 'Test title',
            'name' => 'Test name',
        );
        $request->query->add($params);

        $this->_object->getFilterManager()->addFilter(
            $this->_object->getFilterManager()->newFilter('Column\BasicFilter')
                ->setField('title')
        );

        $this->_object->getFilterManager()->addFilter(
            $this->_object->getFilterManager()->newFilter('Column\BasicFilter')
                ->setField('name')
        );

        $filtersArray = $this->_object->requestFilters($request);
        $this->assertSame($filtersArray, $params);
    }

}