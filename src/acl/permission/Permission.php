<?php

class ACL_Permission extends Record
{

	protected $_resource;
	protected $_role;

	public function setResource(ACL_Resource $resource)
	{
		$this->_resource = $resource;
		$this->id_acl_resource = ACL::instance()->getResourcePathString($resource);
	}

	public function getResource()
	{
		if (!$this->_resource) {
			$this->_resource = ACL::instance()->getResource($this->id_acl_resource);
		}
		return $this->_resource;
	}

	public function setRole(ACL_Role $role)
	{
		$this->_role = $role;
		$this->id_acl_role = ACL::instance()->getRoleID($role);
	}

	public function getRole()
	{
		if (!$this->_role) {
			$this->_role = ACL::instance()->getRole($this->id_acl_role);
		}
		return $this->_role;
	}

}