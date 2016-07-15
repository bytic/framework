<?php

namespace Nip\Tests\Records\Relations;

use Nip\Records\Relations\BelongsTo;
use Mockery as m;

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

        $this->_user = new \Nip_Record();

        $users = m::namedMock('Users', 'Nip_Records')->shouldDeferMissing()
            ->shouldReceive('instance')->andReturnSelf()
            ->shouldReceive('findOne')->andReturn($this->_user)->getMock();
        $users->setPrimaryFK('id_user');
//        m::namedMock('User', 'Nip_Record');

        $this->_object->setWith($users);
        $article = new \Nip_Record();
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