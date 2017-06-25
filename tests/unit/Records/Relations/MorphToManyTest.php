<?php

namespace Nip\Tests\Unit\Records\Relations;

use Mockery as m;
use Nip\Database\Connections\Connection;
use Nip\Records\Record;
use Nip\Records\RecordManager;
use Nip\Records\Relations\MorphToMany;

/**
 * Class MorphToManyTest
 * @package Nip\Tests\Unit\Records\Relations
 */
class MorphToManyTest extends \Codeception\TestCase\Test
{

    public function testNewQuery()
    {
        $tagsManager = new RecordManager();
        $tagsManager->setTable('tags');

        $tag = new Record();
        $tag->id = 3;

        $page = $this->getPageTestRecord();

        $relation = new MorphToMany();
        $relation->setItem($tag);
        $relation->setManager($tagsManager);
        $relation->setWith($page->getManager());

        static::assertSame(
            $relation->newQuery()->getString(),
            'SELECT * FROM `pages`, ``.`pages-tags` WHERE `pages-tags`.`id_page` = `pages`.`id`'
        );
    }

    /**
     * @return Record
     */
    protected function getPageTestRecord()
    {
        $page = new Record();
        $page->id = 1;

        /** @var \Nip\Records\RecordManager $pages */
        $pages = m::namedMock('Pages', \Nip\Records\RecordManager::class)->shouldDeferMissing()
            ->shouldReceive('instance')->andReturnSelf()
            ->shouldReceive('findOne')->andReturn($page)->getMock();
        $pages->setPrimaryKey('id');
        $pages->setPrimaryFK('id_page');

        $page->setManager($pages);
        return $page;
    }

    protected function _before()
    {
        app()->set('db.connection', new Connection(false));
    }
}