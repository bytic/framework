<?php

namespace Nip\Tests\Records\Relations;

use Nip\Records\Relations\BelongsTo;
use Mockery as m;
use Nip\Records\RecordManager as Records;
use Nip\Records\Record;

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

    protected function _after()
    {
    }

    // tests


    public function testInitResults()
    {
        $this->assertSame($this->_user, $this->_object->getResults());
    }

}