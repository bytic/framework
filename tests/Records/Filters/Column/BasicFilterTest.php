<?php

namespace Nip\Tests\Records\Filters\Column;

use Nip\Records\Filters\Column\BasicFilter;
use Nip\Request;

/**
 * Class BasicFilterTest
 * @package Nip\Tests\Records\Filters\Column
 */
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
     * @param $requestField
     * @param $requestValue
     * @param $filterValue
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
     * @param $requestValue
     * @param $filterValue
     * @param $hasValue
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

    /**
     * @return array
     */
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
        parent::setUp();
        $this->_object = new BasicFilter();
    }
}
