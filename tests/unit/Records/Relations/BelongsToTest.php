<?php

namespace Nip\Tests\Unit\Records\Relations;

use Mockery as m;
use Nip\Records\Record;
use Nip\Records\Relations\BelongsTo;

class BelongsToTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var BelongsTo
     */
    protected $_object;

    public function testInitResults()
    {
        static::assertSame($this->_user, $this->_object->getResults());
    }

    protected function _before()
    {
        $this->_object = new BelongsTo();
        $this->_object->setName('User');

        $this->_user = new Record();

        $users = m::namedMock('Users', 'Nip\Records\RecordManager')->shouldDeferMissing()
            ->shouldReceive('instance')->andReturnSelf()
            ->shouldReceive('findOne')->andReturn($this->_user)->getMock();
        $users->setPrimaryFK('id_user');
//        m::namedMock('User', 'Record');

        $this->_object->setWith($users);
        $article = new Record();
        $article->id_user = 3;
        $this->_object->setItem($article);
    }

    // tests

    protected function _after()
    {
    }
}
