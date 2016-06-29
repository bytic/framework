<?php

namespace Nip\Tests\Database;

use Nip_DB_Wrapper;

class WrapperTest extends \Codeception\TestCase\Test
{
	/**
	 * @var \UnitTester
	 */
	protected $tester;

	/**
	 * @var Nip_DB_Wrapper
	 */
	protected $_object;

	protected function _before()
	{
		$this->_object = new Nip_DB_Wrapper();
	}

	public function testInitializesQuerySelect()
	{
		$query = $this->_object->newQuery('select');
		$this->assertInstanceOf('Nip_DB_Query_Abstract',$query);
	}
}