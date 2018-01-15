<?php

namespace Nip\Tests\Unit\Database\Query\Condition;

use Mockery as m;
use Nip\Database\Connection;
use Nip\Database\Query\Select as SelectQuery;

class ConditionTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Connection
     */
    protected $_object;

    /**
     * @var SelectQuery
     */
    protected $_query;

    public function setUp()
    {
        parent::setUp();

        $adapterMock = m::mock('Nip\Database\Adapters\MySQLi')->shouldDeferMissing();
        $adapterMock->shouldReceive('cleanData')->andReturnUsing(function ($data) {
            return $data;
        });

        $this->_object = new Connection();
        $this->_object->setAdapter($adapterMock);
        $this->_query = $this->_object->newQuery();
    }

    public function testParseString()
    {
        $condition = $this->_query->getCondition('name = value');
        static::assertEquals('name = value', $condition->getString());

        $condition = $this->_query->getCondition('id = ?', 5);
        static::assertEquals('id = 5', $condition->getString());

        $condition = $this->_query->getCondition('MATCH title AGAINST (?)', 'lorem ipsum');
        static::assertEquals("MATCH title AGAINST ('lorem ipsum')", $condition->getString());

        $condition = $this->_query->getCondition('pos BETWEEN ? AND ?', [1, 10]);
        static::assertEquals('pos BETWEEN 1 AND 10', $condition->getString());
    }

    public function testAndConditions()
    {
        $condition = $this->_query->getCondition("name LIKE '%lorem%'");
        $condition = $condition->and_($this->_query->getCondition('date > NOW()'));

        static::assertEquals("name LIKE '%lorem%' AND date > NOW()", $condition->getString());
    }

    public function testOrConditions()
    {
        $condition = $this->_query->getCondition("name LIKE '%lorem%'")->or_($this->_query->getCondition('date > NOW()'));
        static::assertEquals("name LIKE '%lorem%' OR date > NOW()", $condition->getString());
    }

    public function testAndOrConditions()
    {
        $condition1 = $this->_query->getCondition("name LIKE '%lorem%'")->and_($this->_query->getCondition('date > NOW()'));
        $condition2 = $this->_query->getCondition("name LIKE '%ipsum%'")->and_($this->_query->getCondition('date < NOW()'));
        $condition3 = $condition1->or_($condition2);

        static::assertEquals("(name LIKE '%lorem%' AND date > NOW()) OR (name LIKE '%ipsum%' AND date < NOW())",
            $condition3->getString());
    }

    public function testNestedConditions()
    {
        $condition2 = $this->_query->getCondition('date > NOW()')->or_($this->_query->getCondition("date < '24.10.2008'"));
        $condition = $this->_query->getCondition("name LIKE '%lorem%'")->and_($condition2);

        static::assertEquals("name LIKE '%lorem%' AND (date > NOW() OR date < '24.10.2008')", $condition->getString());
    }

    public function testWhereIn()
    {
        $condition = $this->_query->getCondition('id in ?', [1, 2, 3]);
        static::assertEquals('id in (1, 2, 3)', $condition->getString());
    }
}
