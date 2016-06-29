<?php

namespace Nip\Tests;

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

    protected function _before()
    {
        $this->_object = new Request();
    }

    protected function _after()
    {
    }

    // tests

    public function testAttributeSet()
    {
        $this->_object->setAttribute('bar','foo');

        $this->assertEquals('foo', $this->_object->attributes->get('bar'));
        $this->assertEquals('foo', $this->_object->get('bar'));
    }

    public function testQuerySet()
    {
        $this->_object->query->set('bar','foo');

        $this->assertEquals('foo', $this->_object->query->get('bar'));
        $this->assertEquals('foo', $this->_object->get('bar'));
    }

    public function testBodySet()
    {
        $this->_object->body->set('bar','foo');

        $this->assertEquals('foo', $this->_object->body->get('bar'));
        $this->assertEquals('foo', $this->_object->get('bar'));
    }

    public function testInitialize()
    {
        $get['var1'] = 'value1';
        $get['var2'] = 'value2';
        $post['var3'] = 'value3';
        $post['var4'] = 'value4';
        $attributes['var4'] = 'value44';

        $this->_object->initialize($get, $post, $attributes);

        $this->assertEquals($get, $this->_object->query->all());
        $this->assertEquals($post, $this->_object->body->all());
        $this->assertEquals($attributes, $this->_object->attributes->all());
    }

    public function testGetOrder()
    {
        $this->testInitialize();

        $this->assertEquals('value44', $this->_object->get('var4'));
    }

    public function testCreateFromGlobals()
    {
        $_GET['foo1'] = 'bar1';
        $_POST['foo2'] = 'bar2';
        $_COOKIE['foo3'] = 'bar3';
        $_FILES['foo4'] = array('bar4');
        $_SERVER['foo5'] = 'bar5';
        
        $request = Request::createFromGlobals();
        
        $this->assertEquals('bar1', $request->query->get('foo1'), '::fromGlobals() uses values from $_GET');
        $this->assertEquals('bar2', $request->body->get('foo2'), '::fromGlobals() uses values from $_POST');
        $this->assertEquals('bar3', $request->cookies->get('foo3'), '::fromGlobals() uses values from $_COOKIE');
        $this->assertEquals(array('bar4'), $request->files->get('foo4'), '::fromGlobals() uses values from $_FILES');
        $this->assertEquals('bar5', $request->server->get('foo5'), '::fromGlobals() uses values from $_SERVER');
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
        $this->assertEquals('action2', $duplicateAction->getActionName());
        $this->assertEquals('controller1', $duplicateAction->getControllerName());
        $this->assertEquals('module1', $duplicateAction->getModuleName());

        $duplicateAction = $request->duplicateWithParams('action2', 'controller2');
        $this->assertEquals('action2', $duplicateAction->getActionName());
        $this->assertEquals('controller2', $duplicateAction->getControllerName());
        $this->assertEquals('module1', $duplicateAction->getModuleName());

        $duplicateAction = $request->duplicateWithParams('action2', 'controller2', 'module2');
        $this->assertEquals('action2', $duplicateAction->getActionName());
        $this->assertEquals('controller2', $duplicateAction->getControllerName());
        $this->assertEquals('module2', $duplicateAction->getModuleName());
    }
    


}