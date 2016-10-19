<?php

namespace Nip\Tests\Unit;

use Nip\Config\Config;
use Nip\Staging;

/**
 * Class StagingTest
 * @package Nip\Tests\Unit
 */
class StagingTest extends AbstractTest
{

    /**
     * @var Staging
     */
    protected $object;

    public function testIsInPublicStages()
    {
        foreach (['production', 'staging', 'demo'] as $stage) {
            static::assertTrue($this->object->isInPublicStages($stage));
        }
        static::assertFalse($this->object->isInPublicStages('local'));
        static::assertFalse($this->object->isInPublicStages('localhost'));
    }

    public function testNewStageProduction()
    {
        $stageName = 'production';
        $newStage = $this->object->newStage($stageName);

        $config = new Config(['STAGE' => ['type' => 'production']]);
        $newStage->setConfig($config);

        static::assertInstanceOf('\Nip\Staging\Stage', $newStage);
        static::assertEquals($stageName, $newStage->getName());
        static::assertTrue($newStage->inProduction());
        static::assertTrue($newStage->isPublic());
    }

    // tests

    public function testNewStageLocal()
    {
        $stageName = 'local';
        $newStage = $this->object->newStage($stageName);

        static::assertInstanceOf('\Nip\Staging\Stage', $newStage);
        static::assertEquals($stageName, $newStage->getName());
        static::assertFalse($newStage->inProduction());
        static::assertFalse($newStage->isPublic());
    }

    protected function setUp()
    {
        parent::setUp();
        $this->object = new Staging();
    }
}
