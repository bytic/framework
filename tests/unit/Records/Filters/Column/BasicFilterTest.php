<?php

namespace Nip\Tests\Records\Filters\Column;

use Nip\Records\Filters\Column\BasicFilter;
use Nip\Request;

class BasicFilterTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var BasicFilter
     */
    protected $_object;

    public function testGetName()
    {
        $this->_object->setField('title');

        $this->assertEquals($this->_object->getName(), 'title');
    }

    public function testOverwriteFieldGetName()
    {
        $this->_object->setField('title');

        $this->assertEquals($this->_object->getName(), 'title');

        $this->_object->setField('title2');

        $this->assertEquals($this->_object->getName(), 'title');
    }

    /**
     * @dataProvider getValueFromRequestProvider
     */
    public function testGetValueFromRequest($requestField, $requestValue, $filterValue)
    {
        $request = new Request();
        $request->query->set($requestField, $requestValue);

        $this->_object->setField('title');
        $this->_object->setRequest($request);

        $this->assertSame($filterValue, $this->_object->getValueFromRequest());
    }

    public function getValueFromRequestProvider()
    {
        return array(
            array('title', 'value', 'value'),
            array('title', 'value', 'value'),
            array('title2', 'value', false),
        );
    }

    /**
     * @dataProvider testHasGetValueProvider
     */
    public function testHasGetValue($requestValue, $filterValue, $hasValue)
    {
        $request = new Request();
        $request->query->set('title', $requestValue);

        $this->_object->setField('title');
        $this->_object->setRequest($request);

        $this->assertSame($filterValue, $this->_object->getValue());
        $this->assertSame($hasValue, $this->_object->hasValue());
    }

    public function testHasGetValueProvider()
    {
        return array(
            array('value', 'value', true),
            array('value ', 'value', true),
            array(' value ', 'value', true),
            array('  ', false, false),
        );
    }

    protected function _before()
    {
        $this->_object = new BasicFilter();
    }


}