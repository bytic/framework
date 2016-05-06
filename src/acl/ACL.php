<?php

class ACL
{

	protected $_resources;
	protected $_roles;
	protected $_permissions;
	protected $_permissionMap;
	protected $_permissionTypes = array("view", "edit", "delete");

	public function addResource($resource)
	{
		$this->_resources[$this->getResourcePathString($resource)] = $resource;
	}

	public function addRole($role)
	{
		$this->_roles[$this->getRoleID($role)] = $role;
	}

	/**
	 * Adds $type acces for $role to $resource
	 * 
	 * @param mixed $resource
	 * @param mixed $role
	 * @param string $type
	 */
	public function addPermission($resource, $role, $type = "view")
	{
		$resource = $this->getResource($resource);
		$role = $this->getRole($role);
		$this->_permissionMap[$this->getRoleID($role)][$this->getResourcePathString($resource)][$type] = true;
	}

	/**
	 * Stores $type access for $role to $resource
	 * Checks to see if permission already exists before insertion
	 * 
	 * @param mixed $resource
	 * @param mixed $role
	 * @param string $type
	 */
	public function insertPermission($resource, $role, $type)
	{
		if (!$this->check($role, $resource, $type)) {
			$permission = ACL_Permissions::instance()->getNew();

			$permission->setRole($this->getRole($role));
			$permission->setResource($this->getResource($resource));

			$permission->type = $type;
			$permission->insert();

			$this->addPermission($resource, $role, $type);
		}
	}

	/**
	 * Remove all permissions for $role [to $resource]
	 * @param mixed $role
	 */
	public function removePermissions($role = false, $resource = false)
	{
		$permissions = $this->getPermissions($role, $resource);
		if ($permissions) {
			foreach ($permissions as $permission) {
				$permission->delete();
			}
		}
		if ($role) {
			if ($resource) {
				unset($this->_permissionMap[$this->getRoleID($role)][$this->getResourcePathString($resource)]);
			} else {
				unset($this->_permissionMap[$this->getRoleID($role)]);
			}
		} else {
			if ($resource) {
				foreach ($this->_permissionMap as $roleID => $resources) {
					unset($this->_permissionMap[$roleID][$this->getResourcePathString($resource)]);
				}
			}
		}
	}

	/**
	 * Checks if $role has $type acces to $resource
	 *
	 * @param mixed $role
	 * @param mixed $resource
	 * @param string $type
	 * @return bool
	 */
	public function check($role, $resource, $type)
	{
		$role = $this->getRoleID($role);
		$resource = $this->getResourcePathString($resource);

		return $this->_permissionMap[$role][$resource][$type] == true;
	}

	/**
	 * @param mixed $resource
	 * @return ACL_Resource
	 */
	public function getResource($resource)
	{
		if (!($resource instanceof ACL_Resource)) {
			$resource = $this->_resources[$resource];
		}
		return $resource;
	}

	public function getResourcePathString($resource)
	{
		return $this->getResource($resource)->getPathString();
	}

	public function getRole($role)
	{
		if (!($role instanceof ACL_Role)) {
			$role = $this->_roles[$role];
		}
		return $role;
	}

	public function getRoleID($role)
	{
		return $this->getRole($role)->getRoleID();
	}

	public function getPermissions($role = false, $resource = false)
	{
		if (!$this->_permissions) {
			$permissions = ACL_Permissions::instance()->findAll();
			if ($permissions) {
				foreach ($permissions as $permission) {
					$this->addPermission($permission->id_acl_resource, $permission->id_acl_role, $permission->type);
				}
			}
			$this->_permissions = $permissions;
		}

		$return = array();
		if ($this->_permissions) {
			foreach ($this->_permissions as $permission) {
				if ($role) {
					if ($permission->id_acl_role != $this->getRoleID($role)) {
						continue;
					}
				}
				if ($resource) {
					if ($permission->id_acl_resource != $this->getResourcePathString($resource)) {
						continue;
					}
				}

				$return[] = $permission;
			}
		}
		return $return;
	}

	public function getResources()
	{
		if (!$this->_resources) {
			$resources = ACL_Resources::instance()->findAll();
			if ($resources) {
				foreach ($resources as $resource) {
					$this->addResource($resource);
				}
			}
		}
		return $this->_resources;
	}

	public function getRoles()
	{
		if (!$this->_roles) {
			$roles = ACL_Roles::instance()->findAll();

			if ($roles) {
				foreach ($roles as $role) {
					$this->addRole($role);
				}
			}
		}
		return $this->_roles;
	}

	public function getPermissionTypes()
	{
		return $this->_permissionTypes;
	}

	/**
	 * Singleton
	 * @return ACL
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