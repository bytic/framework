<?php

namespace Nip\Tests\Unit\Utility\Traits;

use Nip\Utility\Traits\NameWorksTrait;

class NameWorksTraitTest extends \Codeception\TestCase\Test
{
    use NameWorksTrait;


    public function testGetClassName()
    {
        self::assertSame('Nip\Tests\Unit\Utility\Traits\NameWorksTraitTest', $this->getClassName());

        $name = 'Userrs';
        $this->setClassName($name);
        self::assertSame($name, $this->getClassName());
    }

    public function testIsNamespaced()
    {
        self::assertTrue($this->isNamespaced());
    }

    public function testGetNamespaceParentFolder()
    {
        self::assertSame('Traits', $this->getNamespaceParentFolder());
        self::assertSame(['Nip', 'Tests', 'Unit', 'Utility', 'Traits', 'NameWorksTraitTest'],
            $this->getClassNameParts());
    }
}