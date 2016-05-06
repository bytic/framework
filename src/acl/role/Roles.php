<?php

class ACL_Roles extends Records
{

	protected $_hasMany = array("Users");

	/**
	 * Singleton
	 *
	 * @return ACL_Roles
	 */
	public static function instance()
	{
		static $instance;
		if (!($instance instanceof self)) {
			$instance = new self();
		}
		return $instance;
	}

}