<?php

namespace Nip\Tests\Unit\Database;

use Nip\Database\Connection;

class ConnectionTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Connection
     */
    protected $_object;

    public function testNewAdapter()
    {
        static::assertInstanceOf('Nip\Database\Adapters\MySQLi', $this->_object->newAdapter('MySQLi'));
    }

    public function testGetAdapterClass()
    {
        static::assertEquals('\Nip\Database\Adapters\MySQL', $this->_object->getAdapterClass('MySQL'));
        static::assertEquals('\Nip\Database\Adapters\MySQLi', $this->_object->getAdapterClass('MySQLi'));
    }

    public function testNewQueryProvider()
    {
        $types = ['select', 'insert', 'delete'];
        $return = [];
        foreach ($types as $type) {
            $return[] = [$type, 'Nip\Database\Query\\'.ucfirst($type)];
        }
        return $return;
    }

    /**
     * @dataProvider testNewQueryProvider
     * @param $type
     * @param $class
     */
    public function testNewQuery($type, $class)
    {
        $query = $this->_object->newQuery($type);
        static::assertInstanceOf($class, $query);
    }

    protected function _before()
    {
        $this->_object = new Connection();
    }
}