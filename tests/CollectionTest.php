<?php

namespace Nip\Tests;

use Nip\Collection as Collection;
use stdClass;

/**
 * Class CollectionTest.
 */
class CollectionTest extends AbstractTest
{
    /**
     * @var Collection
     */
    protected $collection;

    public function testAdd()
    {
        static::assertEquals(0, count($this->collection));

        $this->collection['first'] = new stdClass();
        static::assertEquals(1, count($this->collection));

        $this->collection['luke'] = 'Luke Skywalker';
        static::assertEquals('Luke Skywalker', $this->collection['luke']);

        $this->collection['third'] = new stdClass();
        static::assertEquals(3, count($this->collection));
    }

    public function testRemove()
    {
        $this->collection[] = 'Darth Vader';
        $this->collection[] = 'Luke Skywalker';
        $this->collection[] = 'Han Solo';

        static::assertEquals(3, count($this->collection));

        unset($this->collection[1]);
        static::assertEquals(2, count($this->collection));
    }

    public function testIterate()
    {
        $items = [
            'darth' => 'Darth Vader',
            'luke'  => 'Luke Skywalker',
            'han'   => 'Han Solo',
        ];
        foreach ($items as $key => $value) {
            $this->collection[$key] = $value;
        }

        foreach ($this->collection as $key => $item) {
            static::assertEquals($items[$key], $item);
        }
    }

    public function testArrayAccess()
    {
        $this->collection[] = 'Darth Vader';
        $this->collection[] = 'Luke Skywalker';
        $this->collection[] = 'Han Solo';

        static::assertEquals('Darth Vader', $this->collection->rewind());
        static::assertEquals('Luke Skywalker', $this->collection->next());
        static::assertEquals('Han Solo', $this->collection->end());
    }

    protected function setUp()
    {
        $this->collection = new Collection();
    }
}
