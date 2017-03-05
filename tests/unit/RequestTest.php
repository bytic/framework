<?php

namespace Nip\Tests\Unit;

use Nip\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class RequestTest
 * @package Nip\Tests\Unit
 */
class RequestTest extends AbstractTest
{

    /**
     * @var \Nip\Request
     */
    protected $request;

    public function testAttributeSet()
    {
        $this->request->setAttribute('bar', 'foo');

        static::assertEquals('foo', $this->request->attributes->get('bar'));
        static::assertEquals('foo', $this->request->get('bar'));
    }

    public function testQuerySet()
    {
        $this->request->query->set('bar', 'foo');

        static::assertEquals('foo', $this->request->query->get('bar'));
        static::assertEquals('foo', $this->request->get('bar'));
    }

    // tests

    public function testBodySet()
    {
        $this->request->request->set('bar', 'foo');

        static::assertEquals('foo', $this->request->request->get('bar'));
        static::assertEquals('foo', $this->request->get('bar'));
    }

    public function testGetOrder()
    {
        $this->testInitialize();

        static::assertEquals('value44', $this->request->get('var4'));
    }

    public function testInitialize()
    {
        $get['var1'] = 'value1';
        $get['var2'] = 'value2';
        $post['var3'] = 'value3';
        $post['var4'] = 'value4';
        $attributes['var4'] = 'value44';

        $this->request->initialize($get, $post, $attributes);

        static::assertEquals($get, $this->request->query->all());
        static::assertEquals($post, $this->request->request->all());
        static::assertEquals($attributes, $this->request->attributes->all());
    }

    public function testCreateFromGlobals()
    {
        $_GET['foo1'] = 'bar1';
        $_POST['foo2'] = 'bar2';
        $_COOKIE['foo3'] = 'bar3';
        $_FILES['foo4'] = ['bar4'];
        $_SERVER['foo5'] = 'bar5';

        $request = Request::createFromGlobals();

        static::assertEquals('bar1', $request->query->get('foo1'), '::fromGlobals() uses values from $_GET');
        static::assertEquals('bar2', $request->request->get('foo2'), '::fromGlobals() uses values from $_POST');
        static::assertEquals('bar3', $request->cookies->get('foo3'), '::fromGlobals() uses values from $_COOKIE');
        static::assertEquals(['bar4'], $request->files->get('foo4'), '::fromGlobals() uses values from $_FILES');
        static::assertEquals('bar5', $request->server->get('foo5'), '::fromGlobals() uses values from $_SERVER');
    }

    public function testDuplicateWithParams()
    {
        $request = new Request();
        $request->setActionName('action1');
        $request->setControllerName('controller1');
        $request->setModuleName('module1');
        $attributes = ['attrb1' => 'val1', 'attrb2' => 'val2'];
        $request->attributes->add($attributes);

        $duplicateAction = $request->duplicateWithParams('action2');
        static::assertEquals('action2', $duplicateAction->getActionName());
        static::assertEquals('controller1', $duplicateAction->getControllerName());
        static::assertEquals('module1', $duplicateAction->getModuleName());

        $duplicateAction = $request->duplicateWithParams('action2', 'controller2');
        static::assertEquals('action2', $duplicateAction->getActionName());
        static::assertEquals('controller2', $duplicateAction->getControllerName());
        static::assertEquals('module1', $duplicateAction->getModuleName());

        $duplicateAction = $request->duplicateWithParams('action2', 'controller2', 'module2');
        static::assertEquals('action2', $duplicateAction->getActionName());
        static::assertEquals('controller2', $duplicateAction->getControllerName());
        static::assertEquals('module2', $duplicateAction->getModuleName());
    }

    public function testCreateFromGlobalsFiles()
    {
        $_FILES = [
            'file1' => [
                'name' => 'MyFile.txt',
                'type' => 'text/plain',
                'tmp_name' => codecept_data_dir('Request/php1h4j1o'),
                'error' => UPLOAD_ERR_OK,
                'size' => 123,
            ],
            'file2' => [
                'name' => 'MyFile.jpg',
                'type' => 'image/jpeg',
                'tmp_name' => codecept_data_dir('Request/php1h4j1o'),
                'error' => UPLOAD_ERR_OK,
                'size' => 300,
            ],
        ];

        $request = Request::createFromGlobals();

        static::assertInstanceOf(
            UploadedFile::class,
            $request->files->get('file2'),
            '::fromGlobals() uses values from $_FILES');
    }

    public function testIsMalicious()
    {
        $this->request = Request::create('/wp-admin/');
        static::assertTrue($this->request->isMalicious());

        $this->request = Request::create('/controller/action');
        static::assertFalse($this->request->isMalicious());
    }

    protected function setUp()
    {
        parent::setUp();
        $this->request = new Request();
    }
}
