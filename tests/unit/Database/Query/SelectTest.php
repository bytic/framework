<?php

namespace Nip\Tests\Database\Query;

use Mockery as m;
use Nip_DB_Wrapper;
use Nip_DB_Query_Select;

class SelectTest extends \Codeception\TestCase\Test
{
	/**
	 * @var \UnitTester
	 */
	protected $tester;

	/**
	 * @var Nip_DB_Wrapper
	 */
	protected $_db;

	/**
	 * @var Nip_DB_Query_Select
	 */
	protected $_object;

	protected function setUp()
	{
		parent::setUp();
		$this->_object = new Nip_DB_Query_Select();

        $adapterMock = m::mock('Nip_DB_Adapters_MySQLi')->shouldDeferMissing();
        $adapterMock->shouldReceive('cleanData')->andReturnUsing(function ($data) {
            return $data;
        });
		$this->_db = new Nip_DB_Wrapper($adapterMock);
		$this->_object->setManager($this->_db);
	}

	public function testSelectSimple()
	{
		$array = array('id, name as new_name', 'table2.test', 'MAX(pos) as pos');
		call_user_func_array(array($this->_object, 'cols'), $array);
		$this->_object->from('table x')->where('id = 5');

		$this->assertEquals(
			'SELECT id, name as new_name, table2.test, MAX(pos) as pos FROM table x WHERE id = 5',
			$this->_object->assemble());
	}

	public function testSimpleSelectDistinct()
	{
		$this->_object->cols('id, name')->options('distinct')->from('table x')->where('id = 5');
		$this->assertEquals(
			"SELECT DISTINCT id, name FROM table x WHERE id = 5",
			$this->_object->assemble());
	}

	public function testWhereAndWhere()
	{
		$this->_object->cols('id, name')->from('table x');
		$this->_object->where('id = 5')->where("active = 'yes'");
		$this->assertEquals(
			"SELECT id, name FROM table x WHERE id = 5 AND active = 'yes'",
			$this->_object->assemble());
	}

	public function testWhereOrWhere()
	{
		$this->_object->cols('id, name')->from('table x');
		$this->_object->where('id = 5')->orWhere('id = 7');
		$this->assertEquals(
			"SELECT id, name FROM table x WHERE id = 5 OR id = 7",
			$this->_object->assemble());
	}

	public function testInitializeCondition()
	{
		$condition = $this->_object->getCondition("lorem ipsum");
		$this->assertThat($condition, $this->isInstanceOf("Nip_DB_Query_Condition"));
	}

	public function testNested()
	{
		$this->_object->from("table1");

		$query = $this->_db->newQuery();
		$query->from("table2");
		$query->where("id != 5");

		$this->_object->where("id NOT IN ?", $query);

		$this->assertEquals("SELECT * FROM `table1` WHERE id NOT IN (SELECT * FROM `table2` WHERE id != 5)", $this->_object->assemble());
	}

	public function testUnion()
	{
//		$this->_object->from("table1");
//
//		$query = $this->_db->newQuery();
//		$query->from("table2");
//
//		$union = $this->_object->union($query);
//
//		$this->assertEquals("SELECT * FROM `table1` UNION SELECT * FROM `table2`", $union->assemble());
	}

}