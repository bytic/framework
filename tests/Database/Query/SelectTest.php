<?php

namespace Nip\Tests\Database\Query;

use Mockery as m;
use Nip\Database\Connection;
use Nip\Database\Query\Select;
use Nip\Tests\AbstractTest;

/**
 * Class SelectTest
 * @package Nip\Tests\Database\Query
 */
class SelectTest extends AbstractTest
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Connection
     */
    protected $_db;

    /**
     * @var Select
     */
    protected $_object;

    public function testSelectSimple()
    {
        $array = ['id, name as new_name', 'table2.test', 'MAX(pos) as pos'];
        call_user_func_array([$this->_object, 'cols'], $array);
        $this->_object->from('table x')->where('id = 5');

        static::assertEquals(
            'SELECT id, name as new_name, table2.test, MAX(pos) as pos FROM table x WHERE id = 5',
            $this->_object->assemble());
    }

    public function testSimpleSelectDistinct()
    {
        $this->_object->cols('id, name')->options('distinct')->from('table x')->where('id = 5');
        static::assertEquals(
            'SELECT DISTINCT id, name FROM table x WHERE id = 5',
            $this->_object->assemble());
    }

    public function testWhereAndWhere()
    {
        $this->_object->cols('id, name')->from('table x');
        $this->_object->where('id = 5')->where("active = 'yes'");
        static::assertEquals(
            "SELECT id, name FROM table x WHERE id = 5 AND active = 'yes'",
            $this->_object->assemble());
    }

    public function testHasPart()
    {
        $this->_object->cols('id, name');
        self::assertTrue($this->_object->hasPart('cols'));

        $this->_object->setCols('id, name');
        self::assertTrue($this->_object->hasPart('cols'));

        $this->_object->limit('');
        self::assertFalse($this->_object->hasPart('limit'));

        $this->_object->limit('6');
        self::assertTrue($this->_object->hasPart('limit'));

        self::assertFalse($this->_object->hasPart('where'));
    }

    public function testLimit()
    {
        $this->_object->cols('id, name')->from('table x');
        $this->_object->where('id = 5')->where("active = 'yes'");
        $this->_object->limit(5);

        static::assertEquals(
            "SELECT id, name FROM table x WHERE id = 5 AND active = 'yes' LIMIT 5",
            $this->_object->assemble()
        );

        $this->_object->limit(5, 10);
        static::assertEquals(
            "SELECT id, name FROM table x WHERE id = 5 AND active = 'yes' LIMIT 5,10",
            $this->_object->assemble()
        );
    }

    public function testWhereOrWhere()
    {
        $this->_object->cols('id, name')->from('table x');
        $this->_object->where('id = 5')->orWhere('id = 7');
        static::assertEquals(
            'SELECT id, name FROM table x WHERE id = 5 OR id = 7',
            $this->_object->assemble());
    }

    public function testInitializeCondition()
    {
        $condition = $this->_object->getCondition('lorem ipsum');
        static::assertThat($condition, $this->isInstanceOf("Nip\Database\Query\Condition\Condition"));
    }

    public function testNested()
    {
        $this->_object->from('table1');

        $query = $this->_db->newQuery();
        $query->from('table2');
        $query->where('id != 5');

        $this->_object->where('id NOT IN ?', $query);

        static::assertEquals('SELECT * FROM `table1` WHERE id NOT IN (SELECT * FROM `table2` WHERE id != 5)',
            $this->_object->assemble());
    }

//    public function testUnion()
//    {
        //		$this->_object->from("table1");
//
//		$query = $this->_db->newQuery();
//		$query->from("table2");
//
//		$union = $this->_object->union($query);
//
//		static::assertEquals("SELECT * FROM `table1` UNION SELECT * FROM `table2`", $union->assemble());
//    }

    protected function setUp()
    {
        parent::setUp();
        $this->_object = new Select();

        $adapterMock = m::mock('Nip\Database\Adapters\MySQLi')->shouldDeferMissing();
        $adapterMock->shouldReceive('cleanData')->andReturnUsing(function ($data) {
            return $data;
        });
        $this->_db = new Connection();
        $this->_db->setAdapter($adapterMock);
        $this->_object->setManager($this->_db);
    }
}
