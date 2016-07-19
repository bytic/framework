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