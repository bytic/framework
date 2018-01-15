<?php

namespace Nip\Tests\Unit\Records\Collections;

use Nip\Records\Collections\Collection as RecordCollection;
use Nip_Records;

class CollectionTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var RecordCollection
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
//		$this->_collection = new RecordCollection();
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
//		static::assertEquals(2, count($this->_collection));
//
//		$this->_collection->delete();
//		static::assertEquals(0, count($this->_collection));
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
//		static::assertEquals(2, count($this->_collection));
//
//		$this->_collection->remove($this->_joe);
//
//		static::assertEquals(1, count($this->_collection));
//		static::assertSame($this->_mac, $this->_collection->rewind());
//	}
//
//	public function tearDown()
//	{
////        var_dump($this->_joe);
//		$this->_joe->delete();
//		$this->_mac->delete();
//	}
}
