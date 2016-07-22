<?php

namespace Nip\Tests\Records\Collections;

use Nip_RecordCollection;
use Nip_Records;

class RecordCollectionTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var \Nip_RecordCollection
     */
    protected $_object;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

//	public function setUp()
//	{
//		$this->_collection = new Nip_RecordCollection();
//		$this->_records = new Nip_Records();
//
//		$this->_joe = $this->_records->getNew(array("id_company" => rand(999, 9999), "email" => rand(9999, 99999)));
//		$this->_mac = $this->_records->getNew(array("id_company" => rand(999, 9999), "email" => rand(9999, 99999)));
//	}
//
//	public function testDelete()
//	{
//		$this->_joe->insert();
//		$this->_mac->insert();
//
//		$this->_collection[] = $this->_joe;
//		$this->_collection[] = $this->_mac;
//		
//		$this->assertEquals(2, count($this->_collection));
//
//		$this->_collection->delete();
//		$this->assertEquals(0, count($this->_collection));
//	}
//
//	public function testRemove()
//	{
//		$this->_joe->insert();
//		$this->_mac->insert();
//
//		$this->_collection[] = $this->_joe;
//		$this->_collection[] = $this->_mac;
//
//		$this->assertEquals(2, count($this->_collection));
//
//		$this->_collection->remove($this->_joe);
//
//		$this->assertEquals(1, count($this->_collection));
//		$this->assertSame($this->_mac, $this->_collection->rewind());
//	}
//
//	public function tearDown()
//	{
////        var_dump($this->_joe);
//		$this->_joe->delete();
//		$this->_mac->delete();
//	}
}