<?php

class InflectorTest extends  \Codeception\TestCase\Test
{

	protected function setUp()
	{
		$this->_inflector = Nip_Inflector::instance();
	}

	public function providerClassTable()
	{
		return array(
			array("users", "Users"),
			array("user_groups", "UserGroups"),
			array("acl-permissions", "Acl_Permissions"),
			array("user_groups-users", "UserGroups_Users")
		);
	}

	public function providerURLController()
	{
		return array(
			array('user_groups', 'UserGroupsController'),
			array('async-user_groups', 'Async_UserGroupsController'),
			array('modal-users', 'Modal_UsersController'),
			array('users', 'UsersController')
		);
	}

	/**
	 * @dataProvider providerClassTable
	 */
	public function testClassToTable($table, $class)
	{
		$this->assertEquals($table, $this->_inflector->unclassify($class));
	}

	/**
	 * @dataProvider providerClassTable
	 */
	public function testTableToClass($table, $class)
	{
		$this->assertEquals($class, $this->_inflector->classify($table));
	}

	/**
	 * @dataProvider providerURLController
	 */
	public function testURLToController($url, $controller)
	{
		$this->assertEquals($controller, $this->_inflector->classify($url) . "Controller");
	}

	/**
	 * @dataProvider providerURLController
	 */
	public function testControllerToURL($url, $controller)
	{
		$class = str_replace("Controller", "", $controller);
		$this->assertEquals($url, $this->_inflector->unclassify($class));
	}


	public function testPluralize()
	{
		$this->assertEquals("mice", $this->_inflector->pluralize("mouse"));
		$this->assertEquals("company", $this->_inflector->pluralize("companies"));
		$this->assertEquals("people", $this->_inflector->pluralize("person"));
		$this->assertEquals("scos", $this->_inflector->pluralize("sco"));
		$this->assertEquals("statuses", $this->_inflector->pluralize("status"));
	}
}