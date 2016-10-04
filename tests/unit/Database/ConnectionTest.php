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
        $this->assertInstanceOf('Nip\Database\Adapters\MySQLi', $this->_object->newAdapter('MySQLi'));
    }

    public function testGetAdapterClass()
    {
        $this->assertEquals('\Nip\Database\Adapters\MySQL', $this->_object->getAdapterClass('MySQL'));
        $this->assertEquals('\Nip\Database\Adapters\MySQLi', $this->_object->getAdapterClass('MySQLi'));
    }

    public function testNewQueryProvider()
    {
        $types = array('select', 'insert', 'delete');
        $return = array();
        foreach ($types as $type) {
            $return[] = array($type, 'Nip\Database\Query\\'.ucfirst($type));
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
        $this->assertInstanceOf($class, $query);
    }

    protected function _before()
    {
        $this->_object = new Connection();
    }
}