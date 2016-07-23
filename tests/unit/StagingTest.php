<?php

namespace Nip\Tests;

class StagingTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var \Nip\Staging
     */
    protected $_object;

    protected function _before()
    {
        $this->_object = new \Nip\Staging();
    }

    protected function _after()
    {
    }

    // tests
    public function testIsInPublicStages()
    {
        foreach (array('production', 'staging', 'demo') as $stage) {
            $this->assertTrue($this->_object->isInPublicStages($stage));
        }
        $this->assertFalse($this->_object->isInPublicStages('local'));
        $this->assertFalse($this->_object->isInPublicStages('localhost'));
    }

    public function testNewStageProduction()
    {
        $stageName = 'production';
        $newStage = $this->_object->newStage($stageName);

        $config = $newStage->newConfig();
        $STAGE = new \stdClass();
        $STAGE->type = 'production';
        $config->set('STAGE', $STAGE);
        $newStage->setConfig($config);

        $this->assertInstanceOf('\Nip\Staging\Stage', $newStage);
        $this->assertEquals($stageName, $newStage->getName());
        $this->assertTrue($newStage->inProduction());
        $this->assertTrue($newStage->isPublic());
    }

    public function testNewStageLocal()
    {
        $stageName = 'local';
        $newStage = $this->_object->newStage($stageName);

        $this->assertInstanceOf('\Nip\Staging\Stage', $newStage);
        $this->assertEquals($stageName, $newStage->getName());
        $this->assertFalse($newStage->inProduction());
        $this->assertFalse($newStage->isPublic());
    }
    
}