<?php

namespace Nip\Tests\Records\Relations;

use Mockery as m;
use Nip\Database\Connections\Connection;
use Nip\Records\Record;
use Nip\Records\RecordManager;
use Nip\Records\Relations\MorphToMany;

/**
 * Class MorphToManyTest
 * @package Nip\Tests\Records\Relations
 */
class MorphToManyTest extends \Nip\Tests\AbstractTest
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
        $relation->setJoinFields(['test']);

        static::assertSame(
            "SELECT `pages`.`id` AS `id`, `pages`.`id_page` AS `id_page`, `pages_pivot`.`test` AS `__test` "
            . "FROM `pages`, ``.`pages_pivot` "
            . "WHERE `pages_pivot`.`id_page` = `pages`.`id` AND `pages_pivot`.`pivotal_type` = ''",
            $relation->newQuery()->getString()
        );
    }

    /**
     * @return Record
     */
    protected function getPageTestRecord()
    {
        $page = new Record();
        $page->id = 1;

        /** @var RecordManager $pages */
        $pages = m::namedMock('Pages', RecordManager::class)->shouldDeferMissing()
            ->shouldReceive('instance')->andReturnSelf()
            ->shouldReceive('findOne')->andReturn($page)
            ->shouldReceive('getFields')->andReturn(['id','id_page'])
            ->getMock();
        $pages->setPrimaryKey('id');
        $pages->setPrimaryFK('id_page');

        $page->setManager($pages);
        return $page;
    }

    protected function setUp()
    {
        parent::setUp();
        app()->set('db.connection', new Connection(false));
    }
}
