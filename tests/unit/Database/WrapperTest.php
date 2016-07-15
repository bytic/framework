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