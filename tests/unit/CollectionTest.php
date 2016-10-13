<?php

class CollectionTest extends \Codeception\TestCase\Test
{

    public function setUp()
    {
        $this->_collection = new Nip_Collection();
    }

    public function testAdd()
    {
        $this->assertEquals(0, count($this->_collection));

        $this->_collection['first'] = new stdClass();
        $this->assertEquals(1, count($this->_collection));

        $this->_collection['luke'] = "Luke Skywalker";
        $this->assertEquals("Luke Skywalker", $this->_collection["luke"]);

        $this->_collection['third'] = new stdClass();
        $this->assertEquals(3, count($this->_collection));
    }

    public function testRemove()
    {
        $this->_collection[] = "Darth Vader";
        $this->_collection[] = "Luke Skywalker";
        $this->_collection[] = "Han Solo";

        $this->assertEquals(3, count($this->_collection));

        unset($this->_collection[1]);
        $this->assertEquals(2, count($this->_collection));
    }

    public function testIterate()
    {
        $items = array(
            "darth" => "Darth Vader",
            "luke" => "Luke Skywalker",
            "han" => "Han Solo"
        );
        foreach ($items as $key => $value) {
            $this->_collection[$key] = $value;
        }

        foreach ($this->_collection as $key => $item) {
            $this->assertEquals($items[$key], $item);
        }
    }

    public function testArrayAccess()
    {
        $this->_collection[] = "Darth Vader";
        $this->_collection[] = "Luke Skywalker";
        $this->_collection[] = "Han Solo";

        $this->assertEquals("Darth Vader", $this->_collection->rewind());
        $this->assertEquals("Luke Skywalker", $this->_collection->next());
        $this->assertEquals("Han Solo", $this->_collection->end());
    }

}