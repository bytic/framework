<?php

namespace Nip\Tests\Records\Filters\Column;

use Nip\Records\Filters\Column\BasicFilter;
use Nip\Request;

class BasicFilterTest extends \Nip\Tests\AbstractTest
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

        static::assertEquals($this->_object->getName(), 'title');
    }

    public function testOverwriteFieldGetName()
    {
        $this->_object->setField('title');

        static::assertEquals($this->_object->getName(), 'title');

        $this->_object->setField('title2');

        static::assertEquals($this->_object->getName(), 'title');
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

        static::assertSame($filterValue, $this->_object->getValueFromRequest());
    }

    public function getValueFromRequestProvider()
    {
        return [
            ['title', 'value', 'value'],
            ['title', 'value', 'value'],
            ['title2', 'value', false],
        ];
    }

    /**
     * @dataProvider hasGetValueProvider
     */
    public function testHasGetValue($requestValue, $filterValue, $hasValue)
    {
        $request = new Request();
        $request->query->set('title', $requestValue);

        $this->_object->setField('title');
        $this->_object->setRequest($request);

        static::assertSame($filterValue, $this->_object->getValue());
        static::assertSame($hasValue, $this->_object->hasValue());
    }

    public function hasGetValueProvider()
    {
        return [
            ['value', 'value', true],
            ['value ', 'value', true],
            [' value ', 'value', true],
            ['  ', false, false],
        ];
    }

    protected function setUp()
    {
        $this->_object = new BasicFilter();
    }
}