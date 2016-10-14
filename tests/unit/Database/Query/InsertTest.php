<?php

namespace Nip\Tests\Unit\Database\Query;

use Mockery as m;
use Nip\Database\Connection;
use Nip\Database\Query\Insert;

class InsertTest extends \Codeception\TestCase\Test
{
	/**
	 * @var \UnitTester
	 */
	protected $tester;

	/**
	 * @var Nip_DB_object_Insert
	 */
	protected $_object;

	public function testOnDuplicate()
	{
		$this->_object->table("table");
		$this->_object->data(array("id" => 3, "name" => "Lorem Ipsum"));
		$this->_object->onDuplicate(array("id" => array("VALUES(`id`)", false), "name" => array("VALUES(`name`)", false)));

        static::assertEquals("INSERT INTO `table` (`id`,`name`) VALUES (3, 'Lorem Ipsum') ON DUPLICATE KEY UPDATE `id` = VALUES(`id`), `name` = VALUES(`name`)",
            $this->_object->assemble());
	}

	public function testMultiple()
	{
		$this->_object->table("table");

		$items = array(
			array("name" => "Lorem Ipsum", "telephone" => 1234),
			array("name" => "Dolor sit amet", "telephone" => 5678)
		);

		foreach ($items as $item) {
			$this->_object->data($item);
		}

        static::assertEquals("INSERT INTO `table` (`name`,`telephone`) VALUES ('Lorem Ipsum', 1234), ('Dolor sit amet', 5678)",
            $this->_object->assemble());
    }

    protected function setUp()
    {
        parent::setUp();
        $this->_object = new Insert();


        $adapterMock = m::mock('Nip\Database\Adapters\MySQLi')->shouldDeferMissing();
        $adapterMock->shouldReceive('cleanData')->andReturnUsing(function ($data) {
            return $data;
        });
        $manager = new Connection();
        $manager->setAdapter($adapterMock);
        $this->_object->setManager($manager);
	}
}