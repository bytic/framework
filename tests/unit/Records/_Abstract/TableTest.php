<?php

namespace Records\_Abstract;

class TableTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $stub = $this->getMockForAbstractClass('Nip\Records\_Abstract\Table');
        $this->_object = $stub;
    }

    protected function _after()
    {
    }

    // tests

    public function testSetModel()
    {
        $this->_object->setModel('Row');
        $this->assertEquals($this->_object->getModel(),'Row');

        $this->_object->setModel('Row2');
        $this->assertEquals($this->_object->getModel(),'Row2');
    }

    public function testGenerateModelClass()
    {
        $this->assertEquals($this->_object->generateModelClass('Notifications\Table'),'Notifications\Row');
        $this->assertEquals($this->_object->generateModelClass('Notifications_Tables'),'Notifications_Table');
        $this->assertEquals($this->_object->generateModelClass('Notifications'),'Notification');
        $this->assertEquals($this->_object->generateModelClass('Persons'),'Person');
    }
}