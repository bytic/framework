<?php

namespace Nip\Tests\Database;

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

    protected function _before()
    {
        $this->_object = new Connection();
    }

    public function testNewAdapter()
    {
        $this->assertInstanceOf('Nip\Database\Adapters\MySQLi', $this->_object->newAdapter('MySQLi'));
    }

    public function testGetAdapterClass()
    {
        $this->assertEquals('\Nip\Database\Adapters\MySQL', $this->_object->getAdapterClass('MySQL'));
        $this->assertEquals('\Nip\Database\Adapters\MySQLi', $this->_object->getAdapterClass('MySQLi'));
    }

    public function testInitializesQueryProvider()
    {
        $types = array('select', 'insert', 'delete');
        $return = array();
        foreach ($types as $type) {
            $return[] = array($type, 'Nip_DB_Query_'.ucfirst($type));
        }
        return $return;
    }

    /**
     * @dataProvider testInitializesQueryProvider
     * @param $type
     * @param $class
     */
    public function testInitializesQuery($type, $class)
    {
        $query = $this->_object->newQuery($type);
        $this->assertInstanceOf($class, $query);
    }
}