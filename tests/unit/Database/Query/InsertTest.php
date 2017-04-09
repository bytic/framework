<?php

namespace Nip\Tests\Unit\Database\Query;

use Mockery as m;
use Nip\Database\Connections\Connection;
use Nip\Database\Query\Insert;

/**
 * Class InsertTest
 * @package Nip\Tests\Unit\Database\Query
 */
class InsertTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Insert
     */
    protected $object;

    public function testOnDuplicate()
    {
        $this->object->table("table");
        $this->object->data(["id" => 3, "name" => "Lorem Ipsum"]);
        $this->object->onDuplicate([
            "id" => ["VALUES(`id`)", false],
            "name" => ["VALUES(`name`)", false]
        ]);

        static::assertEquals(
            "INSERT INTO `table` (`id`,`name`) VALUES (3, 'Lorem Ipsum') ON DUPLICATE KEY UPDATE `id` = VALUES(`id`), `name` = VALUES(`name`)",
            $this->object->assemble()
        );
    }

    public function testMultiple()
    {
        $this->object->table("table");

        $items = [
            ["name" => "Lorem Ipsum", "telephone" => 1234],
            ["name" => "Dolor sit amet", "telephone" => 5678]
        ];

        foreach ($items as $item) {
            $this->object->data($item);
        }

        static::assertEquals(
            "INSERT INTO `table` (`name`,`telephone`) VALUES ('Lorem Ipsum', 1234), ('Dolor sit amet', 5678)",
            $this->object->assemble()
        );
    }

    protected function setUp()
    {
        parent::setUp();
        $this->object = new Insert();

        $adapterMock = m::mock('Nip\Database\Adapters\MySQLi')->shouldDeferMissing();
        $adapterMock->shouldReceive('cleanData')->andReturnUsing(function ($data) {
            return $data;
        });
        $manager = new Connection(false);
        $manager->setAdapter($adapterMock);
        $this->object->setManager($manager);
    }
}
