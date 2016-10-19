<?php

namespace Nip\Tests\Unit;

use Nip\Request;

class RequestTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var \Nip\Request
     */
    protected $_object;

    public function testAttributeSet()
    {
        $this->_object->setAttribute('bar','foo');

        static::assertEquals('foo', $this->_object->attributes->get('bar'));
        static::assertEquals('foo', $this->_object->get('bar'));
    }

    public function testQuerySet()
    {
        $this->_object->query->set('bar','foo');

        static::assertEquals('foo', $this->_object->query->get('bar'));
        static::assertEquals('foo', $this->_object->get('bar'));
    }

    // tests

    public function testBodySet()
    {
        $this->_object->body->set('bar','foo');

        static::assertEquals('foo', $this->_object->body->get('bar'));
        static::assertEquals('foo', $this->_object->get('bar'));
    }

    public function testGetOrder()
    {
        $this->testInitialize();

        static::assertEquals('value44', $this->_object->get('var4'));
    }

    public function testInitialize()
    {
        $get['var1'] = 'value1';
        $get['var2'] = 'value2';
        $post['var3'] = 'value3';
        $post['var4'] = 'value4';
        $attributes['var4'] = 'value44';

        $this->_object->initialize($get, $post, $attributes);

        static::assertEquals($get, $this->_object->query->all());
        static::assertEquals($post, $this->_object->body->all());
        static::assertEquals($attributes, $this->_object->attributes->all());
    }

    public function testCreateFromGlobals()
    {
        $_GET['foo1'] = 'bar1';
        $_POST['foo2'] = 'bar2';
        $_COOKIE['foo3'] = 'bar3';
        $_FILES['foo4'] = array('bar4');
        $_SERVER['foo5'] = 'bar5';

        $request = Request::createFromGlobals();

        static::assertEquals('bar1', $request->query->get('foo1'), '::fromGlobals() uses values from $_GET');
        static::assertEquals('bar2', $request->body->get('foo2'), '::fromGlobals() uses values from $_POST');
        static::assertEquals('bar3', $request->cookies->get('foo3'), '::fromGlobals() uses values from $_COOKIE');
        static::assertEquals(array('bar4'), $request->files->get('foo4'), '::fromGlobals() uses values from $_FILES');
        static::assertEquals('bar5', $request->server->get('foo5'), '::fromGlobals() uses values from $_SERVER');
    }

    public function testDuplicateWithParams()
    {
        $request = new Request();
        $request->setActionName('action1');
        $request->setControllerName('controller1');
        $request->setModuleName('module1');
        $atributes = array('attrb1' => 'val1', 'attrb2' =>'val2');
        $request->attributes->add($atributes);

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
        $_FILES = array(
            'file1' => array(
                'name' => 'MyFile.txt',
                'type' => 'text/plain',
                'tmp_name' => '/tmp/php/php1h4j1o',
                'error' => UPLOAD_ERR_OK,
                'size' => 123,
            ),
            'file2' => array(
                'name' => 'MyFile.jpg',
                'type' => 'image/jpeg',
                'tmp_name' => '/tmp/php/php1h4j1o',
                'error' => UPLOAD_ERR_OK,
                'size' => 300,
            )
        );

        $request = Request::createFromGlobals();

        $this->isInstanceOf('Nip\Request\Files\Uploaded', $request->files->get('file2'), '::fromGlobals() uses values from $_FILES');
    }

    protected function _before()
    {
        $this->_object = new Request();
    }

    protected function _after()
    {
    }
    
    
}