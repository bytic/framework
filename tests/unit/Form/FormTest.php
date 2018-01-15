<?php

namespace Nip\Tests\Unit\Form;

use Nip\Tests\Unit\AbstractTest;
use Nip_Form as Form;
use Nip_Form_Element_Select as Select;

/**
 * Class FormTest.
 */
class FormTest extends AbstractTest
{
    /**
     * @var Form
     */
    protected $object;

    public function testAddSelect()
    {
        $this->object->addSelect('add_select');
        self::assertInstanceOf(Select::class, $this->object->add_select);
        self::assertInstanceOf(Select::class, $this->object->getElement('add_select'));
    }

    protected function setUp()
    {
        parent::setUp();
        $this->object = new Form();
    }
}
